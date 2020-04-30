#!/usr/bin/env bash
set -e
source ./Scripts/lib/test/parsing.sh
test=("test_parse_and_export" "test_parse_sql_password")
for t in "${test[@]}"; do printf "TEST CASES : %s\n" "$t" && eval "$t"; done; sleep 5
bootargs=""
saved=("$@")
config_args="-c -h -p=pass -s=word --development --connection=test"
config_work_dir=""
usage=("" \
"${cyan}Notice:${nc}The test script." \
"Usage: $0 [-p <password>] [-t <password>] [--travis,--circle [--cov]]" \
"           -p <password>       Exports MYSQL_ROOT_PASSWORD" \
"           -t <password>       Exports MYSQL_PASSWORD" \
"           --travis, --circle  Travis or Circle CI Local Test Workflow" \
"           --cov               Coverage All Tests" \
"           -o, --openshift     [path to a file with a list of variables]" \
"Notice:    Use environment variables from open container/pod and a file if it exists" \
"")
while [[ "$#" > 0 ]]; do case $1 in
  --travis )
    #; Test values
    export DB="Mysql"
    export COLLECT_COVERAGE="false"
    export TRAVIS_OS_NAME="osx"
    export TRAVIS_PHP_VERSION=$(php -v | grep -E "[5-7]\.\\d+\.\\d+" | cut -d " " -f 2 | cut -c 1-3)
    # Abort tests
    exit 0;;
  --circle )
    #; Test values
    export DB="Mysql"
    export COLLECT_COVERAGE="false";;
  --cov )
    export COLLECT_COVERAGE=true;;
  -[hH]*|--help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[pP]*)
    OPTIND=1
    parse_sql_password "MYSQL_ROOT_PASSWORD" "user ${DATABASE_USER}" "$@"
    shift $((OPTIND -1))
    ;;
  -[tT]*)
    OPTIND=1
    parse_sql_password "MYSQL_PASSWORD" "test user ${MYSQL_USER}" "$@"
    shift $((OPTIND -1))
    ;;
  -[vV]*|--verbose )
    set -x
    bootargs="-v ${bootargs}"
    echo "Passed params :  $0 ${saved}";;
  -[oO]*|--openshift )
    bootargs="${bootargs} --openshift"
    config_args="--openshift ${config_args}"
    ;;
  --docker )
    config_args="--docker ${config_args}"
    bootargs="--docker ${bootargs}";;
  *) echo "Unknown parameter, passed $0: $1"; exit 1;;
esac; shift; done
source ./configure.sh ${config_args}
bash -c "./migrate-database.sh -u ${bootargs} -i"
