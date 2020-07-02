#!/usr/bin/env bash
set -e
[ $# -lt 3 ] && echo "Usage : $0 <working-directory> <source-file> <target-file>" && exit 1
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
wd=$1
src=$2
dst=$3
pwd=$(pwd)
cd "$wd"
if [[ -f $dst && -f $src && -n $(command -v md5) ]]; then
# read or operation to define $file1 & $file2 here ...
  val1=$(md5 -q "$src")
  val2=$(md5 -q "$dst")
  tmpval="Z${val1}" ; val1="${tmpval}"
  tmpval="Z${val2}" ; val2="${tmpval}"
  if [ "$val1" != "$val2" ]; then
    # files are not the same, backup
    cp -v "$dst" "${dst}.old"
  else
    echo "${dst} file's already there.."
  fi
fi
cp -v "$src" "$dst"
cd "$pwd"
slogger -st "$0" "${src} copied. Please, review the files.\n"
