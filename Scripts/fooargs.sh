#!/usr/bin/env bash
incFOO_ARGS=${incFOO_ARGS:-0}; if [ $incFOO_ARGS -eq 0 ]; then
  export incFOO_ARGS=1
  source ./Scripts/lib/logging.sh
  source ./Scripts/lib/parsing.sh
  set -eu
  docker=$(parse_arg_exists "--docker" $*)
  travis=$(parse_arg_exists "--travis" $*)
  #; colorize shell script
  nc="\033[0m"
  red="\033[0;31m"
  green="\033[0;32m"
  orange="\033[0;33m"
  cyan="\033[0;36m"
  slogger -st $0 "Loading ${orange}Test environment${nc} : $0..."
  #; Common Environment profile
  [[ ! -e .env || ! -e common.env ]] && printf "Missing environment configuration, please run ./deploy.sh %s --nobuild first." $(arch) && exit 1
  eval $(cat .env common.env | awk 'BEGIN{ FS="\n" }{ print "export " $1 }')
  #; To change  Model/Datasource/Database
  export DB=${DB:-Mysql}
  slogger -st $0 "DB : ${green}${DB}${nc}"
  # Test units :
  #             - Web interface:
  #               URL: http://localhost:8000/index.php?test=1
  #             - Built-in cake Console
  #               $ ./test_cake.sh
  #             - Continuous Integration
  #               $ .circleci/build.sh
  #
  if [ $docker ] || [ $travis ]; then
    export MYSQL_HOST=${MYSQL_HOST:-$(hostname)}
    export PGSQL_HOST=${MYSQL_HOST:-$(hostname)}
  fi
  export MYSQL_HOST=${MYSQL_HOST:-'localhost'}
  export PGSQL_HOST=${PGSQL_HOST:-'localhost'}
  export MYSQL_TCP_PORT=${MYSQL_TCP_PORT:-'3306'}
  export MYSQL_USER=${MYSQL_USER:-'maria'}
  #; To override, use shell parameter -t <password> instead
  [ ! $travis ] && export MYSQL_PASSWORD=${MYSQL_PASSWORD:-'maria-abc'}
  export MYSQL_DATABASE=${MYSQL_DATABASE:-'aria_db'}
  #; To override, use shell parameter -dbase=<name> instead
  export DATABASE_USER=${DATABASE_USER:-'root'}
  #; To override, shell parameter -p=<password> instead
  [ ! $travis ] && export MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-'mariadb'}
  #; To override, use shell parameter -tbase=<name> instead
  export TEST_DATABASE_NAME=${TEST_DATABASE_NAME:-'test'}
  export FTP_SERVICE_HOST=localhost
  export FTP_SERVICE_USER=test
  export FTP_SERVICE_PASSWORD=mypassword
  #; export GET_HASH_PASSWORD=wokUd0mcc
  if [[ $(parse_arg_exists "-[vV]+|--verbose" $*) ]]; then
    echo "MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}"
    echo "MYSQL_PASSWORD=${MYSQL_PASSWORD}"
  fi
  export SERVER_NAME=${SERVER_NAME:-$(hostname)}
fi
