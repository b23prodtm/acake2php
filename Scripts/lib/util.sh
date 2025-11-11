#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
APPDIR="$TOPDIR/app/vendor/cakephp/cakephp/src/"
. init_functions .
patches() {
  [ "$#" -lt 1 ] && echo "Only use relative paths: ${FUNCNAME[0]} '<srcfile-relative-path>'" && exit 1
  for f in "$@"; do
    file="$(basename "${f}")"
    dir="$(dirname "${f}")"
    [ ! -f "$dir/$file" ] && file="${file,,}"
    [ ! -f "$dir/$file" ] && dir="${dir,,}"
    #; sed in Scripts/app folder
    sed -i.old -E -f "$(dirname "${BASH_SOURCE[0]}")/../${f}.sed" "$dir/$file"
  done
}
#; export -f patches
