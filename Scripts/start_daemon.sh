#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
parse_args "$@" <<EOF
flag runner -r --runner
flag docker -d --docker
flag server -s --server
flag create -c --create
flag test -t --test
flag update -u --update
end
EOF
LOG=$(new_cake_log) && log_daemon_msg "$LOG"
MARIADB_SHORT_NAME=$(docker_name "$SECONDARY_HUB")
function wait_for_host() {
	[ "$#" -lt 2 ] && printf "Usage: %s <host> <port>" "${FUNCNAME[0]}" && exit 1
	for i in $(seq 1 10); do
		# shellcheck disable=SC2154
		nc -z "$1" "$2" && log_success_msg "${green}${FUNCNAME[0]}{nc}" && sleep 2 && return 0
		echo -n .
		sleep 1
	done
	# shellcheck disable=SC2154
	log_failure_msg "${red}${FUNCNAME[0]}: Host's unavailable${nc}"
	return 1
}
function run_ps() {
	if "$@" >> "$LOG" 2>&1; then
		log_success_msg "SUCCESS"
	else
		log_failure_msg "FAILED"
	fi
}
if [ -n "$docker" ]; then
	log_daemon_msg "Docker list ${MARIADB_SHORT_NAME} containers ($SECONDARY_HUB)"
	#docker shows only running cid (not -q -a -f)
	maria=$(docker ps -q -f "name=${MARIADB_SHORT_NAME}")
	if [ -z "$maria" ]; then
		docker pull "${SECONDARY_HUB}"
	fi
	CID="$TOPDIR/deployment/images/mysqldb/mysqld/mysqld.cid"
	if [ -f "$CID" ] && [ "$(cat "$CID")" = "$maria" ]; then
		log_daemon_msg "Container $MARIADB_SHORT_NAME running."
	else
		log_daemon_msg "Container $MARIADB_SHORT_NAME already maybe running, was stopped."
		maria_hub=$(docker ps -q -a -f "ancestor=${SECONDARY_HUB}")
		docker stop "$maria" "$maria_hub" >> "$LOG" 2>&1 || true
		docker rm -f "$maria" "$maria_hub" >> "$LOG" 2>&1 || true
		log_daemon_msg "Container $MARIADB_SHORT_NAME 's started up..."
		mysql_credentials=("-e MYSQL_DATABASE=${MYSQL_DATABASE} -e MYSQL_USER=${MYSQL_USER}" "-e MYSQL_PASSWORD=${MYSQL_PASSWORD}" \
		"-e MYSQL_RANDOM_ROOT_PASSWORD=yes")
		[ -z "$(docker network ls -q -f 'name=cake')" ] && docker network create cake
		if docker run --name "$MARIADB_SHORT_NAME" -id \
		--env-file .env -e PUID="$(id -u "$USER")" -e PGID="$(id -g "$USER")" \
		--network cake -e MYSQL_HOST="${MYSQL_HOST}" -e MYSQL_BIND_ADDRESS="${MYSQL_BIND_ADDRESS:-'0.0.0.0'}" \
		"${mysql_credentials[@]}" --publish "$MYSQL_TCP_PORT:$MYSQL_TCP_PORT" \
		-v "$TOPDIR/deployment/images/mysqldb/conf.d:/etc/mysql/conf.d" -v "$TOPDIR/deployment/images/mysqldb/config:/config" \
		-v "$TOPDIR/deployment/images/mysqldb/mysqld:/var/run/mysqld/" \
		"${SECONDARY_HUB}" >> "$LOG" 2>&1; then
			log_daemon_msg "Started docker --name=${MARIADB_SHORT_NAME} ref: $(docker ps -q -a -f "name=maria") host: $MYSQL_HOST}"
		fi
	fi
	if ! wait_for_host "$MYSQL_HOST" "${MYSQL_TCP_PORT:-3306}"; then
		log_daemon_msg "${red}Failed waiting for Mysql${nc}"
	fi
	docker ps -q -f "name=${MARIADB_SHORT_NAME}" > "$CID"
	check_log "$LOG"
fi
# shellcheck disable=SC2086
if [ -n "$server" ]; then
  show_password_status "${MYSQL_USER}" "${MYSQL_PASSWORD}" "is running development server"
  url="http://${SERVER_NAME}:${CAKE_TCP_PORT:-8000}"
  # shellcheck disable=SC2154
  log_daemon_msg "Welcome homepage ${cyan}${url}${nc}"
  log_daemon_msg "Administrator login ${cyan}${url}/admin/index${nc}"
  # shellcheck disable=SC2154
  log_daemon_msg "Debugging echoes ${cyan}${url}${orange}?debug=1&verbose=1${nc}"
  log_daemon_msg "Another Test configuration ${cyan}${url}/admin/index.php${orange}?test=1${nc}"
  log_daemon_msg "Unit tests ${cyan}${url}/test.php${nc}"
  log_daemon_msg "Turnoff flags (fix captcha)${cyan}${url}/admin/logoff.php${nc}"
  log_daemon_msg "==============================================="
  if [ -n "$test" ]; then
    clover=""
    if [ "${COLLECT_COVERAGE}" = "true" ]; then
      run_ps "$TOPDIR/app/vendor/bin/phpunit" --log-junit ~/phpunit/junit.xml --coverage-clover \
      app/build/logs/clover.xml --stop-on-failure -c app/phpunit.xml.dist \
      app/tests/TestCase/AllTestsTest.php
      clover="--coverage-clover app/build/logs/clover.xml"
    elif [ "${PHPCS}" = 1 ]; then
      run_ps "$TOPDIR/app/vendor/bin/phpcs" --colors -p -s --extensions=php --cache "$TOPDIR/app"
    fi
  fi
  # shellcheck disable=SC2086
  run_ps cakephp $@ $clover
elif [ -n "$create" ]; then
  #; cakephp shell
  log_daemon_msg "Migrating database 'cake migrations' ..."
  # shellcheck disable=SC2086
  run_ps cakephp bake migration "$@"
  # shellcheck disable=SC2086
  run_ps cakephp migrations -n status
elif [ -n "$update" ]; then
  #; cakephp shell
  log_daemon_msg "Migrating database 'cake migrations' ..."
  # shellcheck disable=SC2086
  run_ps cakephp migrations -n status "$@"
fi
check_log "$LOG"
