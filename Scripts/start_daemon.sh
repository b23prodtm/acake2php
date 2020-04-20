#!/usr/bin/env bash
source ./Scripts/lib/logging.sh
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
docker=$(parse_arg_exists "--docker" $*)
ck_args=$(parse_arg_trim "-[oO]+|--openshift|--docker" $*)
LOG="/usr/local/var/log/$(basename $0 .sh).$(date +%Y-%m-%d_%H:%M).log" && mkdir -p $(dirname $LOG)
touch $LOG
wait_for_host() {
	[ "$#" -lt 2 ] && printf "Usage: $0 <host> <port>" && exit 1
	for i in `seq 1 10`; do
		nc -z $1 $2 && slogger -st $0 "Success" && return 0
		echo -n .
		sleep 1
	done
	return 1
}
if [ $docker 2> /dev/null ] && [ $(which docker) 2> /dev/null ]; then
	container="betothreeprod/mariadb-${BALENA_MACHINE_NAME:-intel-nuc}"
	slogger -st $0 "Docker list maria containers ($container)"
	#docker quits shell ??
	maria=$(docker ps -q -f "name=maria" 2>&1)
	if [ $maria > $LOG ]; then
		slogger -st $0 "Container can be restarted..."
		maria=$(docker container restart $maria)
	else
		maria=$(docker run --name maria -id -h db --env-file common.env ${container} 2>&1)
	fi
	if [ $maria > $LOG ]; then
		slogger -st $0 "Started docker container --name maria, ref: ${maria}"
		wait_for_host $MYSQL_HOST ${MYSQL_TCP_PORT:-3306}
		[ $? = 1 ] && slogger -st $0 "Failed waiting for Mysql"
	fi
fi
if [ $(parse_arg_exists "server" $ck_args) 2>&1 > $LOG ]; then
   show_password_status "${DATABASE_USER}" "${MYSQL_ROOT_PASSWORD}" "is running development server"
   url="http://localhost:${CAKE_TCP_PORT:-8000}"
  slogger -st $0 "Welcome homepage ${cyan}${url}${nc}"
  slogger -st $0 "Administrator login ${cyan}${url}/admin/index${nc}"
  slogger -st $0 "Debugging echoes ${cyan}${url}${orange}?debug=1&verbose=1${nc}"
  slogger -st $0 "Another Test configuration ${cyan}${url}/admin/index.php${orange}?test=1${nc}"
  slogger -st $0 "Unit tests ${cyan}${url}/test.php${nc}"
  slogger -st $0 "Turnoff flags (fix captcha)${cyan}${url}/admin/logoff.php${nc}"
  slogger -st $0 "==============================================="
   ./lib/Cake/Console/cake $ck_args
elif [ $(parse_arg_exists "test" $(parse_arg_trim "--connection*" $ck_args)) 2>&1 > $LOG ]; then
  slogger -st $0 $(printf "Passed Cake Args: %s" "$ck_args")
  if [[ "${COLLECT_COVERAGE}" == "true" ]]; then
    ./app/Vendor/bin/phpunit --log-junit ~/phpunit/junit.xml --coverage-clover app/build/logs/clover.xml --stop-on-failure -c app/phpunit.xml.dist app/Test/Case/AllTestsTest.php
  elif [ "${PHPCS}" != '1' ]; then
      ./lib/Cake/Console/cake $ck_args
  else
      ./app/Vendor/bin/phpcs -p --extensions=php --standard=CakePHP ./lib/Cake ${ck_args}
  fi
elif [ $(parse_arg_exists "docker-compose" $ck_args) 2>&1 > $LOG ]; then
  if [ ! $(which docker-compose) 2> /dev/null ]; then ./Scripts/install-docker-compose.sh; fi
  [ -z $SERVER_NAME ] && SERVER_NAME=local
  ./Scripts/configure-available-site.sh $SERVER_NAME
  slogger -st $0 "${ck_args}"
  bash -c "${ck_args}"
elif [ $(parse_arg_exists "update" $ck_args) 2>&1 > $LOG ]; then
  #; cakephp shell
  echo "Migrating database 'cake schema update' ..."
	p=$(parse_arg_trim "update" $ck_args)
	slogger -st $0 $(printf "Passed Cake Args:(%s) -> %s" "$ck_args" "$p")
  ./lib/Cake/Console/cake schema update $p -y
  slogger -st $0 "Update finished"
  if [ -f app/Config/Schema/sessions.php ]; then
      slogger -st $0 "Generating default Sessions table"
      ./lib/Cake/Console/cake schema create Sessions $p -y
  fi
  slogger -st $0 "Generating database schema 'cake schema generate'"
  ./lib/Cake/Console/cake schema generate $p -f snapshot
fi
if [[ $(wc -l $LOG | awk '{ print $1 }') -gt 0 ]]; then printf "Find the log file at %s and read more detailed information." $LOG; fi
