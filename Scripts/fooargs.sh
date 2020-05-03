#!/usr/bin/env bash
incFOO_ARGS=${incFOO_ARGS:-0}; if [ $incFOO_ARGS -eq 0 ]; then
  export incFOO_ARGS=1
  source ./Scripts/lib/logging.sh
  source ./Scripts/lib/parsing.sh
  set -eu
  docker=$(parse_arg_exists "--docker" "$@")
  #; colorize shell script
  nc="\033[0m"
  red="\033[0;31m"
  green="\033[0;32m"
  orange="\033[0;33m"
  cyan="\033[0;36m"
  slogger -st $0 "Loading ${orange}Test environment${nc} : $0..."
  #; To change  Model/Datasource/Database
  export DB=${DB:-Mysql}
  [ "${DB}" = "Mysql" ] && export DATABASE_ENGINE=MysqlCms && export DATABASE_SERVICE_NAME=MYSQL
  [ "${DB}" = "Pgsql" ] && export DATABASE_ENGINE=PostgresCms && export DATABASE_SERVICE_NAME=PGSQL
  [ "${DB}" = "Sqlite" ] && export DATABASE_ENGINE=SqliteCms && export DATABASE_SERVICE_NAME=SQLITE
  slogger -st $0 "DB : ${green}${DB}${nc}"
  if [ $docker 2> /dev/null ]; then
    export MYSQL_ROOT_HOST=127.0.0.1
    export PGSQL_SERVICE_HOST=127.0.0.1
  fi
  export MYSQL_ROOT_HOST=${MYSQL_ROOT_HOST:-%}
  export PGSQL_SERVICE_HOST=${PGSQL_SERVICE_HOST:-%}
  export MYSQL_TCP_PORT=3306
  #; To override, use shell parameter -dbase=<name> instead
  export MYSQL_DATABASE=${MYSQL_DATABASE:-foo_db}
  export DATABASE_USER=${DATABASE_USER:-root}
  #; To override, shell parameter -p=<password> instead
  export MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-foo_pass}
  #. Test configuration ?test=1, ./test_cake.sh
  if [ $docker 2> /dev/null ]; then
    export MYSQL_HOST=127.0.0.1
    export TEST_PGSQL_SERVICE_HOST=127.0.0.1
  fi
  export MYSQL_HOST=${MYSQL_HOST:-localhost}
  export TEST_PGSQL_SERVICE_HOST=${TEST_PGSQL_SERVICE_HOST:-localhost}
  #; To override, use shell parameter -tbase=<name> instead
  export TEST_DATABASE_NAME=${TEST_DATABASE_NAME:-test}
  export MYSQL_USER=${MYSQL_USER:-dummy_foo}
  #; To override, use shell parameter -t <password> instead
  export MYSQL_PASSWORD=${MYSQL_PASSWORD:-foo_pass_test}
  export FTP_SERVICE_HOST=localhost
  export FTP_SERVICE_USER=test
  export FTP_SERVICE_PASSWORD=mypassword
  #; More about default environment app/Config/core.php
  #; Openshift Online secure keys (default_keys)
  export CAKEPHP_SECURITY_SALT=${CAKEPHP_SECURITY_SALT:-Word}
  export CAKEPHP_SECURITY_CIPHER_SEED=${CAKEPHP_SECURITY_CIPHER_SEED:-01234}
  #; 0, 1, 2 the higher the more debug data
  export CAKEPHP_DEBUG_LEVEL=${CAKEPHP_DEBUG_LEVEL:-2}
  #; Shell parameters -h -p password -s salt
  #; export GET_HASH_PASSWORD=wokUd0mcc
  export PHP_CMS_DIR=${PHP_CMS_DIR:-app/webroot/php_cms}
  if [[ $(parse_arg_exists "-[vV]+|--verbose" $*) ]]; then
    echo "MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}"
    echo "MYSQL_PASSWORD=${MYSQL_PASSWORD}"
  fi
  export SERVER_NAME=${SERVER_NAME:-$(hostname)}
fi
