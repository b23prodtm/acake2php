#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
function patches() {
  [ "$#" -lt 1 ] && echo "Only use relative paths: ${FUNCNAME[0]} '<srcfile-relative-path>'" && exit 1
  for f in "$@"; do
    file="$(basename "${f}")"
    dir="$(dirname "${f}")"
    [ ! -f "$dir/$file" ] && file="${file,,}"
    [ ! -f "$dir/$file" ] && dir="${dir,,}"
    #; sed in Scripts/app folder
    sed -i.old -E -f "$TOPDIR/Scripts/${f}.sed" "$dir/$file"
    printf "sed %s result %s\n" "$f" "$?"
  done
}
#; export -f patches
