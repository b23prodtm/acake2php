#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
migrate="--connection=test -v -u -i"
# default arg --docker, is enabled
saved=( "$@" )
config_args="-p pass -s hash --development"
db_data="db-data:/config/databases/"
usage=("" \
"${cyan}Notice:${nc}The test script." \
"Usage: $0 [--docker|--runner [--cov|--phpcs]] [-p <password>] [-t <password>] " \
"           -r, --runner        [path to a file with a list of variables], " \
"                               also disables Docker Image" \
"           --docker            [enabled] Start a Docker daemon and DATABASE" \
"           --cov               Coverage All Tests" \
"           --phpcs             PHP Code Sniffer" \
"" \
"Notice:                        Use environment variables from open container/pod" \
"                               and a file if it exists" \
"Default arguments:   " \
"           --docker" \
"")
while [[ "$#" -gt 0 ]]; do case $1 in
  --runner )
    # shellcheck disable=SC2086
    migrate="$migrate --runner"
    # shellcheck disable=SC2086
    config_args="$config_args --runner"
    ;;
  --phpcs )
    export PHPCS=1
    migrate=""
    config_args=""
    ;;
  --cov )
    export COLLECT_COVERAGE=true
    ;;
  -[hH]*|--help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[vV]*|--verbose )
    set -x
    migrate="-v ${migrate}"
    echo "Passed params :  $0 ${saved[*]}"
    ;;
  --docker )
    config_args="--docker ${config_args}"
    migrate="--docker ${migrate}"
    db_data="$(pwd)/mysqld$(echo "${db_data}" | cut -d : -f 2)"
    ;;
  *) echo "Unknown parameter, passed $0: $1"; exit 1;;
esac; shift; done
# shellcheck source=configure.sh
bash -c "${TOPDIR}/configure.sh $config_args"
if bash -c "${TOPDIR}/migrate-database.sh ${migrate}"; then
  log_msg_success "CakePHP Test Suite successfully finished, go on with the job."
else
  log_msg_failure "CakePHP Test Suite had errors. Quit the job thread."
  log_msg_daemon  "Only continuous integration scripts may run tests."
fi
