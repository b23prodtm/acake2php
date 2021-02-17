#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
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
