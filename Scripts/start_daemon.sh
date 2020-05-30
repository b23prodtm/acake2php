#!/usr/bin/env bash
TOPDIR=$(cd `dirname $BASH_SOURCE`/.. && pwd)
source $TOPDIR/Scripts/lib/logging.sh
source $TOPDIR/Scripts/lib/shell_prompt.sh
source $TOPDIR/Scripts/lib/parsing.sh
docker=$(parse_arg_exists "--docker" $*)
ck_args=$(parse_arg_trim "-[oO]+|--openshift|--docker" $*)
travis=$(parse_arg_exists "--travis" $*)
LOG=$(new_log $travis $docker) && slogger -st $0 $LOG
MARIADB_SHORT_NAME=$(echo $SECONDARY_HUB | awk -F/ '{ print $2 }' | awk -F: '{ print $1 }')
MARIADB_CONT_NAME=betothreeprod/${MARIADB_SHORT_NAME}-${BALENA_MACHINE_NAME:-intel-nuc}
wait_for_host() {
	[ "$#" -lt 2 ] && printf "Usage: $FUNCNAME <host> <port>" && exit 1
	for i in `seq 1 10`; do
		nc -z $1 $2 && slogger -st $FUNCNAME "${green}Success${nc}" && sleep 2 && return 0
		echo -n .
		sleep 1
	done
	slogger -st $FUNCNAME "${red}Failed: Host's unavailable${nc}"
	return 1
}
if [ $docker 2> /dev/null ]; then
	slogger -st $0 "Docker list ${MARIADB_SHORT_NAME} containers"
	#docker quits shell ??
	maria=$(docker ps -q -a -f "name=${MARIADB_SHORT_NAME}")
	if [ -z $maria ]; then
		docker pull ${MARIADB_CONT_NAME}
	fi
	CID=$TOPDIR/mysqldb/mysqld/mysqld.cid
	if [ -f $CID ] && [ $(cat $CID) = "$maria" ]; then
		slogger -st $0 "Container $MARIADB_SHORT_NAME OK."
	else
		slogger -st $0 "Container $MARIADB_SHORT_NAME already maybe running, was stopped."
		maria_hub=$(docker ps -q -a -f "ancestor=${SECONDARY_HUB}")
		docker stop $maria $maria_hub >> $LOG 2>&1 || true
		docker rm -f $maria $maria_hub >> $LOG 2>&1 || true
		slogger -st $0 "Container $MARIADB_SHORT_NAME 's started up..."
		mysql_credentials=("-e MYSQL_DATABASE=${MYSQL_DATABASE} -e MYSQL_USER=${MYSQL_USER}" "-e MYSQL_PASSWORD=${MYSQL_PASSWORD}" \
		"-e DATABASE_USER=${DATABASE_USER}" "-e MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}")
		[ ! $(docker network ls -q -f 'name=cake') ] && docker network create cake
		docker run --name $MARIADB_SHORT_NAME -id \
		--env-file .env -e PUID=$(id -u $USER) -e PGID=$(id -g $USER) \
		--network cake -e MYSQL_HOST=${MYSQL_HOST} -e MYSQL_BIND_ADDRESS=${MYSQL_BIND_ADDRESS:-'0.0.0.0'} \
		"${mysql_credentials[@]}" --publish $MYSQL_TCP_PORT:$MYSQL_TCP_PORT \
		-v $TOPDIR/mysqldb/conf.d:/etc/mysql/conf.d -v $TOPDIR/mysqldb/config:/config \
		-v $TOPDIR/mysqldb/mysqld:/var/run/mysqld/ \
		${MARIADB_CONT_NAME} >> $LOG 2>&1
	fi
	if [ $? = 0 ]; then
		slogger -st $0 "Started docker --name=${MARIADB_SHORT_NAME} ref: $(docker ps -q -a -f "name=maria") host: $MYSQL_HOST}"
		wait_for_host $MYSQL_HOST ${MYSQL_TCP_PORT:-3306}
		[ $? = 1 ] && slogger -st $0 "${red}Failed waiting for Mysql${nc}"
	fi
	docker ps -q -a -f "name=${MARIADB_SHORT_NAME}" > $CID
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
  cakephp $ck_args >> $LOG 2>&1
	[ $? = 1 ] && slogger -st $0 "FAILED" || slogger -st $0 "SUCCESS"
elif [ $(parse_arg_exists "test" $(parse_arg_trim "--connection*" $ck_args)) >> $LOG 2>&1 ]; then
  slogger -st $0 $(printf "Passed Cake Args: %s" "$ck_args")
  if [[ "${COLLECT_COVERAGE}" == "true" ]]; then
    $TOPDIR/app/Vendor/bin/phpunit --log-junit ~/phpunit/junit.xml --coverage-clover \
		app/build/logs/clover.xml --stop-on-failure -c app/phpunit.xml.dist app/Test/Case/AllTestsTest.php >> $LOG 2>&1
  elif [ "${PHPCS}" != '1' ]; then
      cakephp $ck_args  --coverage-clover app/build/logs/clover.xml >> $LOG 2>&1
  else
      $TOPDIR/app/Vendor/bin/phpcs -p --extensions=php --standard=CakePHP $TOPDIR/lib/Cake ${ck_args} >> $LOG 2>&1
  fi
	[ $? = 1 ] && slogger -st $0 "FAILED" || slogger -st $0 "SUCCESS"
elif [ $(parse_arg_exists "docker-compose" $ck_args) >> $LOG 2>&1 ]; then
  if [ ! $(which docker-compose) 2> /dev/null ]; then $TOPDIR/Scripts/install-docker-compose.sh; fi
	: ${SERVER_NAME?}
	$TOPDIR/Scripts/configure-available-site.sh $SERVER_NAME
  slogger -st $0 "${ck_args}"
  bash -c "${ck_args}"
elif [ $(parse_arg_exists "update" $ck_args) >> $LOG 2>&1 ]; then
  #; cakephp shell
  slogger -st $0 "Migrating database 'cake schema update' ..."
	p=$(parse_arg_trim "update" $ck_args)
	slogger -st $0 $(printf "Passed Cake Args:(%s) -> %s" "$ck_args" "$p")
  cakephp schema update $p -y >> $LOG 2>&1
  [ $? = 1 ] && slogger -st $0 "FAILED" || slogger -st $0 "SUCCESS"
  if [ -f app/Config/Schema/sessions.php ]; then
      slogger -st $0 "Generating default Sessions table"
      cakephp schema create Sessions $p -y >> $LOG 2>&1
			[ $? = 1 ] && slogger -st $0 "FAILED" || slogger -st $0 "SUCCESS"
	fi
  slogger -st $0 "Generating database schema 'cake schema generate'"
  cakephp schema generate $p -f snapshot >> $LOG 2>&1
	[ $? = 1 ] && slogger -st $0 "FAILED" || slogger -st $0 "SUCCESS"
fi
check_log $LOG
