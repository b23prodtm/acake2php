#!/usr/bin/env bash
set -e
source ./Scripts/lib/test/parsing.sh
test=("test_parse_and_export" "test_parse_sql_password" "test_arg_exists" "test_arg_trim")
for t in "${test[@]}"; do printf "TEST CASES : %s\n" "$t" && eval "$t"; done; sleep 5
bootargs="--docker"
migrate="-i -u --connection=test" 
# BUG ISSUE
#\  --enable-authentication-plugin"
saved=("$@")
config_args="-c -h -p pass -s word --development"
config_work_dir=""
usage=("" \
"${cyan}Notice:${nc}The test script." \
"Usage: $0 [-p <password>] [-t <password>] [--travis,--circle [--cov]]" \
"           -p <password>       Exports MYSQL_ROOT_PASSWORD" \
"           -t <password>       Exports MYSQL_PASSWORD" \
"           --travis, --circle  Travis or Circle CI Local Test Workflow" \
"           --cov               Coverage All Tests" \
"           -o, --openshift     [path to a file with a list of variables]" \
"           --socket            Symlink socket /tmp/mysql.sock" \
"           --docker            Startup Docker Image DATABASE" \
"Notice:    Use environment variables from open container/pod and a file if it exists" \
"")
while [[ "$#" > 0 ]]; do case $1 in
  --travis )
    #; Test values
    export TRAVIS_OS_NAME="osx"
    export TRAVIS_PHP_VERSION=$(php -v | grep -E "[5-7]\.\\d+\.\\d+" | cut -d " " -f 2 | cut -c 1-3)
    # Abort tests
    ;;
  --circle )
    bootargs=$(parse_arg_trim "--docker" $bootargs)
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
    bootargs="-v ${bootargs}"
    echo "Passed params :  $0 ${saved[*]}";;
  -[oO]*|--openshift )
    bootargs=$(parse_arg_trim "--docker" $bootargs)
    bootargs="${bootargs} --openshift"
    config_args="--openshift ${config_args}"
    ;;
  --docker )
    config_args="--docker ${config_args}"
    bootargs="--docker ${bootargs}"
    ;;
  --socket )
    migrate="-Y ${migrate}";;
  *) echo "Unknown parameter, passed $0: $1"; exit 1;;
esac; shift; done
source ./configure.sh ${config_args}
bash -c "./migrate-database.sh ${migrate} ${bootargs}"
