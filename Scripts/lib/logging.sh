#!/usr/bin/env bash
_locate() {
  [ $# -lt 1 ] && echo "Usage: $0 <filename>" && exit 1
  find /usr -name $1 | grep -m 1 $1
}
# export -f _locate
function slogger() {
  [ -f /dev/log ] && logger $@ && return
  [ "$#" -gt 1 ] && shift
  echo -e "$@"
}
#; export -f slogger
function new_log() {
	LOG="/usr/local/var/log/$(basename $0 .sh).$(date +%Y-%m-%d_%H:%M).log" && mkdir -p $(dirname $LOG)
	touch $LOG && echo $LOG
}
#; export -f new_log
function check_log() {
  if [ "$#" -gt 0 ] && [[ $(wc -l $1 | awk '{ print $1 }') -gt 0 ]]; then
    slogger -st $0 "Find the log file at %s and read more detailed information.\n" $1
  fi
}
