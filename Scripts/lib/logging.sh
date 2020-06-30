#!/usr/bin/env bash
. init_functions
function new_cake_log() {
  while [ "$#" -gt 0 ]; do case $1 in
    --travis)
      new_log "${MYPHPCMS_LOG}" "travis.${TRAVIS_BUILD_NUMBER:-'TRAVIS_BUILD_NUMBER'}.log"
      return;;
    --docker)
      new_log "${MYPHPCMS_LOG}" "docker.log"
	    return;;
    -[oO]+|--openshift)
      new_log "${MYPHPCMS_LOG}" "openshift.log"
      return;;
      *)
      ;;
  esac; shift; done
  new_log "${MYPHPCMS_LOG}" "acake2php.log"
}
