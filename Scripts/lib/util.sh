#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
function relative_path() {
  [ "$#" -lt 1 ] && echo "${FUNCNAME[0]} '<srcfile-absolute-path>'" && exit 1
  relative_path="${2#"$1"}"
  echo "${relative_path#./}"
}
#; export -f relative_path
function cake_path() {
  [ "$#" -lt 1 ] && echo "${FUNCNAME[0]} '<CONSTANT>'" && exit 1
  bash -c "php -r '\
    require \"${TOPDIR}/Config/paths.php\"; \
    printf(constant(\"$1\"));\
  '"
}
#; export -f cake_path
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

