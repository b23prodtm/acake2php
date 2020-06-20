#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/test/parsing.sh
. "$TOPDIR/Scripts/lib/test/parsing.sh"
migrate="--connection=test -v -u -i --enable-authentication-plugin"
# default arg --docker, is enabled
saved=("$@")
set -- "--docker" "$@"
config_args="-c -h -p pass -s word --development"
db_data="db-data:/config/databases/"
usage=("" \
"${cyan}Notice:${nc}The test script." \
"Usage: $0 [--travis|--docker|--openshift|--circle [--cov]] [-p <password>] [-t <password>] " \
"           --travis, --circle  Travis or Circle CI Local Test Workflow" \
"                               also disables Docker Image" \
"           --cov               Coverage All Tests" \
"           -o, --openshift     [path to a file with a list of variables], " \
"                               also disables Docker Image" \
"           --docker            [enabled] Startup with Docker Image DATABASE" \
"           -p <password>       Exports MYSQL_ROOT_PASSWORD" \
"           -t <password>       Exports MYSQL_PASSWORD" \
"" \
"Notice:                        Use environment variables from open container/pod" \
"                               and a file if it exists" \
"Default arguments:   " \
"           --docker" \
"")
while [[ "$#" -gt 0 ]]; do case $1 in
  --circle )
    migrate=$(parse_arg_trim "--docker" $migrate)
    config_args=$(parse_arg_trim "--docker" $config_args)
    ;;
  --cov )
    export COLLECT_COVERAGE=true;;
  -[hH]*|--help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[pP]*)
    parse_sql_password "MYSQL_ROOT_PASSWORD" "user ${DATABASE_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[tT]*)
    parse_sql_password "MYSQL_PASSWORD" "test user ${MYSQL_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[vV]*|--verbose )
    set -x
    migrate="-v ${migrate}"
    echo "Passed params :  $0 ${saved[*]}";;
  -[oO]*|--openshift )
    migrate=$(parse_arg_trim "--docker" $migrate)" --openshift"
    config_args=$(parse_arg_trim "--docker" $config_args)" --openshift"
    ;;
  --travis)
    export MYSQL_HOST=${MYSQL_HOST:-'127.0.0.1'}
    export MYSQL_USER='travis'
    export MYSQL_PASSWORD=''
    export MYSQL_ROOT_PASSWORD=''
    migrate=$(parse_arg_trim "--docker" $migrate)" --travis"
    config_args=$(parse_arg_trim "--docker" $config_args)" --travis"
    ;;
  --docker )
    config_args="--docker ${config_args}"
    migrate="--docker ${migrate}"
    db_data="$(pwd)/mysqld$(echo ${db_data} | cut -d : -f 2)"
    ;;
  *) echo "Unknown parameter, passed $0: $1"; exit 1;;
esac; shift; done
# shellcheck source=configure.sh
. "${TOPDIR}/configure.sh" $config_args
if bash -c "${TOPDIR}/migrate-database.sh ${migrate}"; then
  printf "[SUCCESS] CakePHP Test Suite successfully finished, go on with the job...\n"
else
  printf "[FAILED] CakePHP Test Suite had errors. Quit the job thread.\n\
[INFO] Only continuous integration scripts may run tests.\n"
  exit 1
fi
