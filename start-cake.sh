#!/usr/bin/env bash
set -e
source ./Scripts/lib/parsing.sh
source ./Scripts/lib/shell_prompt.sh
command="server -p 8000"
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
"")
while [[ "$#" > 0 ]]; do case $1 in
  --help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[pP]*)
    parse_sql_password "MYSQL_ROOT_PASSWORD" "current ${DATABASE_USER}" "$@"
    shift
    ;;
  -[tT]*)
    parse_sql_password "MYSQL_PASSWORD" "current ${MYSQL_USER}" "$@"
    shift
    ;;
  -[cC]*)
    command=$2
    shift; shift; command="${command} $*"
    parse_and_export "p" "CAKE_TCP_PORT" "specify -p <port>" "$@"
    shift
    ;;
  --docker )
    command="--docker ${command}"
    ;;
  *);;
esac; shift; done
./Scripts/bootstrap.sh $command
