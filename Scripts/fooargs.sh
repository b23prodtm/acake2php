#!/usr/bin/env bash
incFOO_ARGS=${incFOO_ARGS:-0}; if [ "$incFOO_ARGS" -eq 0 ]; then
  export incFOO_ARGS=1
  set -eu
pargs=$(parse_arg_trim "-[dD]+|--docker" "$@")
parse_args $pargs <<EOF
flag docker -d --docker
end
EOF
  log_daemon_msg  "TEST MODE, $0...: $*"
  #; Common Environment profile
  [[ ! -e .env || ! -e common.env ]] \
  && printf "Missing environment configuration, please run ./deploy.sh %s --nobuild first." "$(arch)" \
  && exit 1
  eval "$(cat .env common.env | awk 'BEGIN{ FS="\n" }{ print "export " $1 }')"
  #; To change  Model/Datasource/Database
  export DB=${DB:-Mysql}
  # Test units :
  #             - Web interface:
  #               URL: http://localhost:8000/index.php?test=1
  #             - Built-in cake Console
  #               $ ./test_cake.sh
  #             - Continuous Integration
  #               $ .circleci/build.sh
  #
  if [ -n "$docker" ]; then
    export MYSQL_HOST=${MYSQL_HOST:-$(hostname)}
    export PGSQL_HOST=${MYSQL_HOST:-$(hostname)}
  fi
  export MYSQL_HOST=${MYSQL_HOST:-'localhost'}
  export PGSQL_HOST=${PGSQL_HOST:-'localhost'}
  export MYSQL_TCP_PORT=${MYSQL_TCP_PORT:-'3306'}
  export MYSQL_USER=${MYSQL_USER:-'maria'}
  #; To override, use shell parameter -t <password> instead
  export MYSQL_PASSWORD=${MYSQL_PASSWORD:-'maria-abc'}
  export MYSQL_DATABASE=${MYSQL_DATABASE:-'aria_db'}
  #; To override, use shell parameter -dbase=<name> instead
  export MYSQL_ROOT_USER=${MYSQL_ROOT_USER:-'root'}
  #; To override, shell parameter -p=<password> instead
  export MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-'mariadb'}
  #; To override, use shell parameter -tbase=<name> instead
  export TEST_MYSQL_DATABASE=${TEST_MYSQL_DATABASE:-'test'}
  export MASTER_PASSWORD=password
  if [ -n "$(parse_arg "-[vV]+|--verbose" "$@")" ]; then
    echo "MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}"
    echo "MYSQL_PASSWORD=${MYSQL_PASSWORD}"
  fi
  export SERVER_NAME=${SERVER_NAME:-$(hostname)}
fi
