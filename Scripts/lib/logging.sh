#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
runner=$(parse_arg "-[rR]+|--runner"  "$@")
pargs=$(parse_arg_trim "-[rR]+|--runner"  "$@")
if [ -n "$runner" ]; then
  slogger -st "$0" "Bootargs...: ${pargs}"
  export CAKEPHP_DEBUG_LEVEL=1
  # shellcheck source=bootargs.sh
  . "${TOPDIR}/Scripts/bootargs.sh" "$@"
else
  slogger -st "$0" "Locally Testing values, bootargs...: ${pargs}"
  export CAKEPHP_DEBUG_LEVEL=2
  # shellcheck source=fooargs.sh
  . "${TOPDIR}/Scripts/fooargs.sh" "$@"
fi
#; Make logs folders available
mkdir -p "${MYPHPCMS_LOG}"
function new_cake_log_long() {
  while [ "$#" -gt 0 ]; do case $1 in
    --travis)
      new_log "$TOPDIR/${MYPHPCMS_LOG}" "travis.${TRAVIS_BUILD_NUMBER:-'TRAVIS_BUILD_NUMBER'}.log"
      return;;
    --docker)
      new_log "$TOPDIR/${MYPHPCMS_LOG}" "docker.log"
      return;;
    -[oO]+|--openshift)
      new_log "$TOPDIR/${MYPHPCMS_LOG}" "openshift.log"
      return;;
      *)
      ;;
  esac; shift; done
  new_log "$TOPDIR/${MYPHPCMS_LOG}" "acake2php.log"
}
function new_cake_log() {
  a="$(new_cake_log_long "$@")"
  ln -sf "$a" "$(basename "$a")"
  basename "$a"
}
