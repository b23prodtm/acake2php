#!/usr/bin/env bash
_locate() {
  [ $# -lt 1 ] && echo "Usage: $FUNCNAME <filename>" && exit 1
  find /usr -name $1 | grep -m 1 $1
}
# export -f _locate
function slogger() {
  [ -f /dev/log ] && logger "$@" && return
  [ "$#" -gt 1 ] && shift
  echo -e "$*"
}
#; export -f slogger
function new_log() {
  LOG="${MYPHPCMS_LOG}/$(basename $FUNCNAME .sh).$(date +%Y-%m-%d_%H:%M).log"
  while [ "$#" -gt 0 ]; do case $1 in
    --travis)
    	LOG="${MYPHPCMS_LOG}/travis.${TRAVIS_BUILD_NUMBER:-'TRAVIS_BUILD_NUMBER'}.log"
      ;;
    --docker|-+[o|O]*)
      LOG="${MYPHPCMS_LOG}/docker.$(date +%Y-%m-%d).log"
	    ;;
      *)
      ;;
  esac; shift; done
  mkdir -p $(dirname $LOG)
	touch $LOG && echo $LOG || printf "Please set MYPHPCMS_LOG=%s to a writeable folder !" $MYPHPCMS_LOG
}
#; export -f new_log
function check_log() {
  if [ "$#" -gt 0 ] && [[ $(wc -l $1 | awk '{ print $1 }') -gt 0 ]]; then
    slogger -st $FUNCNAME "Find the log file at %s and read more detailed information.\n" $1
  fi
}
