#!/usr/bin/env bash
_locate() {
  [ $# -lt 1 ] && echo "Usage: $0 <filename>" && return $FALSE
  find /usr -name $1 | grep -m 1 $1
}
# export -f _locate
function archsfile() {
  [ "$#" -lt 2 ] && printf "Usage: %s <arch-file> <rename-arch>" && exit 1
  a=$1; shift
  for pf in "$@"; do
    for i in $(ls -d *); do
      n=$(echo $i | sed -E -e "s/(.*)${pf}(.*)\$/\\1${a}\\2/")
      [ "$i" != "$n" ] && [ ! -d $i ] && mv -f $i $n
    done
  done
}
# export -f archsfile
