#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/test/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
command="--docker -c server -p 8000 -H 0.0.0.0"
saved=( "$@" )
export COLLECT_COVERAGE="false"
usage=("" \
"Usage: $0 [-c <command>] [options]" \
"          -c <command> <options> [--help]" \
"                               Set parameters to lib/Cake/Console/cake" \
"                               E.g. $0 -c --docker server --help" \
"                               Default command is " \
"                               lib/Cake/Console/cake server -p 8000 -H 0.0.0.0" \
"           --disable-docker    Don't start Docker Image DATABASE" \
"")
while [[ "$#" -gt 0 ]]; do case $1 in
  -[hH]*|--help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[vV]*|--verbose )
    set -x
    command="${command} $1"
    echo "Passed params : $0 ${saved[*]}";;
  -[cC]*)
    docker=$(parse_arg "--docker" "$command")
    command="$docker ${*:2}"
    parse_and_export "-p" "CAKE_TCP_PORT" "specify -p <port>" "$@"
    break;;
  --disable-docker )
    # shellcheck disable=SC2086
    command=$(parse_arg_trim --docker $command)
    ;;
  --docker )
    command="$command $1"
    ;;
  *);;
esac; shift; done
bash -c "./Scripts/bootstrap.sh $command"
