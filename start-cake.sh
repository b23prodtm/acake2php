#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/test/parsing.sh
. "$TOPDIR/Scripts/lib/parsing.sh"
# shellcheck source=Scripts/lib/test/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
command="--docker server -p 8000 -H 0.0.0.0"
saved=("$@")
export COLLECT_COVERAGE="false"
usage=("" \
"Usage: $0 [-p <password>] [-t <password>] [-c <command>] [options]" \
"          -p <password>        Exports MYSQL_ROOT_PASSWORD to bootargs." \
"          -t <password>        Exports MYSQL_PASSWORD" \
"          -c <command> <options> [--help]" \
"                               Set parameters to lib/Cake/Console/cake" \
"                               E.g. $0 -c server --help" \
"                               Default command is " \
"                               lib/Cake/Console/cake server -p 8000" \
"           --disable-docker    Don't start Docker Image DATABASE" \
"")
while [[ "$#" -gt 0 ]]; do case $1 in
  --help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[vV]*|--verbose )
    set -x
    command="-v ${command}"
    echo "Passed params : $0 ${saved[*]}";;
  -[pP]*)
    parse_sql_password "MYSQL_ROOT_PASSWORD" "current ${DATABASE_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[tT]*)
    parse_sql_password "MYSQL_PASSWORD" "current ${MYSQL_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[cC]*)
    command="${*:2}"
    parse_and_export "-p" "CAKE_TCP_PORT" "specify -p <port>" "$@"
    break;;
  --disable-docker )
    command=$(parse_arg_trim "--docker" $command)
    ;;
  *);;
esac; shift; done
./Scripts/bootstrap.sh $command
