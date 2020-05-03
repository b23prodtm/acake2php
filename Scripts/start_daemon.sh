#!/usr/bin/env bash
TOPDIR=$(cd `dirname $BASH_SOURCE`/.. && pwd)
source ./Scripts/lib/logging.sh
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
docker=$(parse_arg_exists "--docker" $*)
ck_args=$(parse_arg_trim "-[oO]+|--openshift|--docker" $*)
LOG=$(new_log) && slogger -st $0 $LOG
MARIADB_SHORT_NAME=$(echo $SECONDARY_HUB | awk -F/ '{ print $2 }' | awk -F: '{ print $1 }')
MARIADB_CONT_NAME=betothreeprod/${MARIADB_SHORT_NAME}-${BALENA_MACHINE_NAME:-intel-nuc}
wait_for_host() {
	[ "$#" -lt 2 ] && printf "Usage: $FUNCNAME <host> <port>" && exit 1
	for i in `seq 1 10`; do
		nc -z $1 $2 && slogger -st $FUNCNAME "${green}Success${nc}" && sleep 2 && return 0
		echo -n .
		sleep 1
	done
	return 1
}
if [ $docker 2> /dev/null ]; then
	slogger -st $0 "Docker list ${MARIADB_SHORT_NAME} containers"
	#docker quits shell ??
	maria=$(docker ps -q -a -f "name=${MARIADB_SHORT_NAME}")
	if [ ! -z $maria ]; then
		slogger -st $0 "Container $MARIADB_SHORT_NAME already running, was stopped."
		docker kill $maria >> $LOG 2>&1 || true
	fi
	docker rm -f $maria >> $LOG 2>&1 || true
	slogger -st $0 "Container $MARIADB_SHORT_NAME 's started up..."
	docker run --name $MARIADB_SHORT_NAME -id \
	--env-file common.env --env-file .env \
	-e PUID=$(id -u $USER) -e PGID=$(id -g $USER) \
	-h $MYSQL_HOST --publish $MYSQL_TCP_PORT:$MYSQL_TCP_PORT \
	-v $TOPDIR/mysqldb/config:/config \
	-v $TOPDIR/mysqldb/mysqld:/var/run/mysqld \
	${MARIADB_CONT_NAME} >> $LOG 2>&1
	if [ $? = 0 ]; then
		slogger -st $0 "Started docker --name=${MARIADB_SHORT_NAME} ref: $(docker ps -q -a -f "name=maria") host: $MYSQL_HOST}"
		wait_for_host $MYSQL_HOST ${MYSQL_TCP_PORT:-3306}
		[ $? = 1 ] && slogger -st $0 "${red}Failed waiting for Mysql${nc}"
	fi
		slogger -st $0 "Connect to docker exec -it ${MARIADB_SHORT_NAME} <ENTRYPOINT> .."
		check_log $LOG
fi
if [ $(parse_arg_exists "server" $ck_args) >> $LOG 2>&1 ]; then
  show_password_status "${DATABASE_USER}" "${MYSQL_ROOT_PASSWORD}" "is running development server"
  : ${SERVER_NAME?}
	url="http://${SERVER_NAME}:${CAKE_TCP_PORT:-8000}"
  slogger -st $0 "Welcome homepage ${cyan}${url}${nc}"
  slogger -st $0 "Administrator login ${cyan}${url}/admin/index${nc}"
  slogger -st $0 "Debugging echoes ${cyan}${url}${orange}?debug=1&verbose=1${nc}"
  slogger -st $0 "Another Test configuration ${cyan}${url}/admin/index.php${orange}?test=1${nc}"
  slogger -st $0 "Unit tests ${cyan}${url}/test.php${nc}"
  slogger -st $0 "Turnoff flags (fix captcha)${cyan}${url}/admin/logoff.php${nc}"
  slogger -st $0 "==============================================="
   ./lib/Cake/Console/cake $ck_args
elif [ $(parse_arg_exists "test" $(parse_arg_trim "--connection*" $ck_args)) >> $LOG 2>&1 ]; then
  slogger -st $0 $(printf "Passed Cake Args: %s" "$ck_args")
  if [[ "${COLLECT_COVERAGE}" == "true" ]]; then
    ./app/Vendor/bin/phpunit --log-junit ~/phpunit/junit.xml --coverage-clover app/build/logs/clover.xml --stop-on-failure -c app/phpunit.xml.dist app/Test/Case/AllTestsTest.php
  elif [ "${PHPCS}" != '1' ]; then
      ./lib/Cake/Console/cake $ck_args
  else
      ./app/Vendor/bin/phpcs -p --extensions=php --standard=CakePHP ./lib/Cake ${ck_args}
  fi
elif [ $(parse_arg_exists "docker-compose" $ck_args) >> $LOG 2>&1 ]; then
  if [ ! $(which docker-compose) 2> /dev/null ]; then ./Scripts/install-docker-compose.sh; fi
	: ${SERVER_NAME?}
	./Scripts/configure-available-site.sh $SERVER_NAME
  slogger -st $0 "${ck_args}"
  bash -c "${ck_args}"
elif [ $(parse_arg_exists "update" $ck_args) >> $LOG 2>&1 ]; then
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
check_log $LOG
