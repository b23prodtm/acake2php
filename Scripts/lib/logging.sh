#!/usr/bin/env bash
. init_functions
function new_cake_log() {
  while [ "$#" -gt 0 ]; do case $1 in
    --travis)
      new_log "${MYPHPCMS_LOG}" "travis.${TRAVIS_BUILD_NUMBER:-'TRAVIS_BUILD_NUMBER'}.log"
      ;;
    --docker)
      new_log "${MYPHPCMS_LOG}" "docker.$(date +%Y-%m-%d).log"
	    ;;
    -+[o|O]*)
      new_log "${MYPHPCMS_LOG}" "openshift.log"
      ;;
      *)
      new_log "${MYPHPCMS_LOG}"
      ;;
  esac; shift; done
}
