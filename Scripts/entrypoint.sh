#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
parse_args_lazy "$@" <<EOF
flag runner -r --runner
flag server -s --server
flag create -c --create
flag test -t --test
flag update -u --update
end
EOF
LOG=$(new_cake_log) && log_daemon_msg "$LOG"
function wait_for_host() {
	[ "$#" -lt 2 ] && printf "Usage: %s <host> <port>" "${FUNCNAME[0]}" && exit 1
	for i in $(seq 1 10); do
		# shellcheck disable=SC2154
		nc -z "$1" "$2" && log_success_msg "${FUNCNAME[0]}" && sleep 2 && return 0
		echo -n .
		sleep 1
	done
	# shellcheck disable=SC2154
	log_failure_msg "${FUNCNAME[0]}: Host's unavailable"
	return 1
}
function run_ps() {
	if "$@" >> "$LOG" 2>&1; then
		log_success_msg "SUCCESS"
	else
		log_failure_msg "FAILED"
	fi
}
# shellcheck disable=SC2086
if [ ${#server} -gt 0 ]; then
  show_password_status "${MYSQL_USER}" "${MYSQL_PASSWORD}" "is running development server"
  url="http://${SERVER_NAME}:${CAKE_TCP_PORT:-8000}"
  # shellcheck disable=SC2154
  log_daemon_msg "Welcome homepage ${url}"
  log_daemon_msg "Administrator login ${url}/admin/index"
  # shellcheck disable=SC2154
  log_daemon_msg "Debugging echoes ${url}${orange}?debug=1&verbose=1"
  log_daemon_msg "Another Test configuration ${url}/admin/index.php${orange}?test=1"
  log_daemon_msg "Unit tests ${url}/test.php"
  log_daemon_msg "Turnoff flags (fix captcha)${url}/admin/logoff.php"
  log_daemon_msg "==============================================="
  if [ ${#test} -gt 0 ]; then
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
  run_ps cakephp "$@" "$clover"
elif [ ${#create} -gt 0 ]; then
  #; cakephp shell
  log_daemon_msg "Migrating database 'cake migrations' ..."
  # shellcheck disable=SC2086
  run_ps cakephp bake migration "$@"
  # shellcheck disable=SC2086
  run_ps cakephp migrations -n status
elif [ ${#update} -gt 0 ]; then
  #; cakephp shell
  log_daemon_msg "Migrating database 'cake migrations' ..."
  # shellcheck disable=SC2086
  run_ps cakephp migrations -n status "$@"
fi
check_log "$LOG"
