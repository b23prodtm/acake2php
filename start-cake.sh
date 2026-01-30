#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/test/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
command="-c server -p 8000 -H 0.0.0.0"
parse_args_lazy "$@" <<EOF
flag docker -d --docker
end
EOF

saved=( "$@" )
export COLLECT_COVERAGE="false"
usage() {
  printf "%s\n" \
  "Usage: $0 [-c <command>] [options]" \
  "          -c <command> <options> [--help]" \
  "                               Set parameters to lib/Cake/Console/cake" \
  "                               E.g. $0 -c --docker server --help" \
  "                               Default command is " \
  "                               lib/Cake/Console/cake server -p 8000 -H 0.0.0.0" \
  "           --disable-docker    Don't start Docker Image DATABASE" \
  ""
}
while [[ "$#" -gt 0 ]]; do case $1 in
  -[hH]*|--help )
    usage
    exit 0;;
  -[vV]*|--verbose )
    set -x
    command="${command} $1"
    echo "Passed params : $0 ${saved[*]}";;
  -[cC]*)
    command="${*:2}"
    parse_and_export CAKE_TCP_PORT -p --port "$@"
    break;;
  --disable-docker )
    # shellcheck disable=SC2086
    docker=""
    ;;
  *);;
esac; shift; done
if [ ${#docker} -gt 0 ]; then
  docker="--docker"
fi
bash -c "./Scripts/bootstrap.sh $docker $command"
