#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
# shellcheck source=lib/parsing.sh
. "$TOPDIR/Scripts/lib/parsing.sh"
openshift=$(parse_arg "-[oO]+|--openshift" "$@")
docker=$(parse_arg "--docker" "$@")
travis=$(parse_arg "--travis" "$@")
ck_args=$(parse_arg_trim "-[oO]+|--openshift|--docker|--travis" "$@")
LOG=$(new_cake_log "$travis" "$docker" "$openshift") && slogger -st "$0" "$LOG"
MARIADB_SHORT_NAME=$(docker_name "$SECONDARY_HUB")
function wait_for_host() {
	[ "$#" -lt 2 ] && printf "Usage: %s <host> <port>" "${FUNCNAME[0]}" && exit 1
	for i in $(seq 1 10); do
		# shellcheck disable=SC2154
		nc -z "$1" "$2" && slogger -st "${FUNCNAME[0]}" "${green}Success${nc}" && sleep 2 && return 0
		echo -n .
		sleep 1
	done
	# shellcheck disable=SC2154
	slogger -st "${FUNCNAME[0]}"  "${red}Failed: Host's unavailable${nc}"
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
	slogger -st "$0" "Docker list ${MARIADB_SHORT_NAME} containers ($SECONDARY_HUB)"
	#docker shows only running cid (not -q -a -f)
	maria=$(docker ps -q -f "name=${MARIADB_SHORT_NAME}")
	if [ -z "$maria" ]; then
		docker pull "${SECONDARY_HUB}"
	fi
	CID="$TOPDIR/deployment/images/mysqldb/mysqld/mysqld.cid"
	if [ -f "$CID" ] && [ "$(cat "$CID")" = "$maria" ]; then
		slogger -st "$0" "Container $MARIADB_SHORT_NAME running."
	else
		slogger -st "$0" "Container $MARIADB_SHORT_NAME already maybe running, was stopped."
		maria_hub=$(docker ps -q -a -f "ancestor=${SECONDARY_HUB}")
		docker stop "$maria" "$maria_hub" >> "$LOG" 2>&1 || true
		docker rm -f "$maria" "$maria_hub" >> "$LOG" 2>&1 || true
		slogger -st "$0" "Container $MARIADB_SHORT_NAME 's started up..."
		mysql_credentials=("-e MYSQL_DATABASE=${MYSQL_DATABASE} -e MYSQL_USER=${MYSQL_USER}" "-e MYSQL_PASSWORD=${MYSQL_PASSWORD}" \
		"-e DATABASE_USER=${DATABASE_USER}" "-e MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}")
		[ -z "$(docker network ls -q -f 'name=cake')" ] && docker network create cake
		if docker run --name "$MARIADB_SHORT_NAME" -id \
		--env-file .env -e PUID="$(id -u "$USER")" -e PGID="$(id -g "$USER")" \
		--network cake -e MYSQL_HOST="${MYSQL_HOST}" -e MYSQL_BIND_ADDRESS="${MYSQL_BIND_ADDRESS:-'0.0.0.0'}" \
		"${mysql_credentials[@]}" --publish "$MYSQL_TCP_PORT:$MYSQL_TCP_PORT" \
		-v "$TOPDIR/deployment/images/mysqldb/conf.d:/etc/mysql/conf.d" -v "$TOPDIR/deployment/images/mysqldb/config:/config" \
		-v "$TOPDIR/deployment/images/mysqldb/mysqld:/var/run/mysqld/" \
		"${SECONDARY_HUB}" >> "$LOG" 2>&1; then
			slogger -st "$0" "Started docker --name=${MARIADB_SHORT_NAME} ref: $(docker ps -q -a -f "name=maria") host: $MYSQL_HOST}"
		fi
	fi
	if ! wait_for_host "$MYSQL_HOST" "${MYSQL_TCP_PORT:-3306}"; then
		slogger -st "$0" "${red}Failed waiting for Mysql${nc}"
	fi
	docker ps -q -f "name=${MARIADB_SHORT_NAME}" > "$CID"
	check_log "$LOG"
fi
# shellcheck disable=SC2086
if [ -n "$(parse_arg "server" $ck_args)" ]; then
  show_password_status "${DATABASE_USER}" "${MYSQL_ROOT_PASSWORD}" "is running development server"
  url="http://${SERVER_NAME}:${CAKE_TCP_PORT:-8000}"
	# shellcheck disable=SC2154
  slogger -st "$0" "Welcome homepage ${cyan}${url}${nc}"
  slogger -st "$0" "Administrator login ${cyan}${url}/admin/index${nc}"
	# shellcheck disable=SC2154
	slogger -st "$0" "Debugging echoes ${cyan}${url}${orange}?debug=1&verbose=1${nc}"
  slogger -st "$0" "Another Test configuration ${cyan}${url}/admin/index.php${orange}?test=1${nc}"
  slogger -st "$0" "Unit tests ${cyan}${url}/test.php${nc}"
  slogger -st "$0" "Turnoff flags (fix captcha)${cyan}${url}/admin/logoff.php${nc}"
  slogger -st "$0" "==============================================="
	# shellcheck disable=SC2086
	run_ps cakephp $ck_args
elif [ -n "$(parse_arg "test" "$(parse_arg_trim "--connection*" $ck_args)")" ]; then
  slogger -st "$0" "$(printf "Passed Cake Args: %s" "$ck_args")"
  if [ "${COLLECT_COVERAGE}" = "true" ]; then
    run_ps "$TOPDIR/app/Vendor/bin/phpunit" --log-junit ~/phpunit/junit.xml --coverage-clover \
		app/build/logs/clover.xml --stop-on-failure -c app/phpunit.xml.dist \
		app/Test/Case/AllTestsTest.php
  elif [ "${PHPCS}" = 1 ]; then
		run_ps "$TOPDIR/app/Vendor/bin/phpcs" --colors -p -s --extensions=php --cache "$TOPDIR/app"
	else
		# shellcheck disable=SC2086
		run_ps cakephp $ck_args --coverage-clover app/build/logs/clover.xml
  fi
elif [ -n "$(parse_arg "update" $ck_args)" ]; then
  #; cakephp shell
  slogger -st "$0" "Migrating database 'cake schema update' ..."
	# shellcheck disable=SC2086
	p=$(parse_arg_trim "update" $ck_args)
	slogger -st "$0" "$(printf "Passed Cake Args:(%s) -> %s" "$ck_args" "$p")"
	# shellcheck disable=SC2086
	run_ps cakephp schema update $p -y
  if [ -f app/Config/Schema/sessions.php ]; then
      slogger -st "$0" "Generating default Sessions table"
			# shellcheck disable=SC2086
			run_ps cakephp schema create Sessions $p -y
	fi
  slogger -st "$0" "Generating database schema 'cake schema generate'"
	# shellcheck disable=SC2086
	run_ps cakephp schema generate $p -f snapshot
fi
check_log "$LOG"
