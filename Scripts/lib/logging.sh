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
