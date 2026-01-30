#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
parse_args_lazy "$@" <<EOF
flag help -h --help
option group -g --group
end
EOF
if [ "$help" -gt 0 ]; then printf "%s\n" "Usage: ${BASH_SOURCE[0]} [-g|--group userGroup]"; exit 0; fi
if [ ${#group} -eq 0 ]; then group="acake2php"; fi

function create_www_path() {
  mkdir -p "$1"
  chmod -Rv 2770 "$1"
  if [ "$(groups | grep "$group")" == "" ]; then
    printf "[WARN] %s\n" "no group named $group"  
  else
    chown -Rv :"$group" "$1"
  fi
}
create_www_path "$TOPDIR/app/webroot/php-cms/e13/etc"
create_www_path "$TOPDIR/app/tmp/"
create_www_path "$TOPDIR/app/tmp/logs"
create_www_path "$TOPDIR/app/tmp/cache"
create_www_path "$TOPDIR/app/tmp/cache/long"
create_www_path "$TOPDIR/app/tmp/cache/persistent"
create_www_path "$TOPDIR/app/tmp/cache/models"
create_www_path "$TOPDIR/app/tmp/sessions"
create_www_path "$TOPDIR/app/logs"
create_www_path "$TOPDIR/log"
touch "$TOPDIR/log/www.access.log"
touch "$TOPDIR/log/www.error.log"
