#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
parse_args_lazy "$@" <<EOF
option group -g --group
flag help -h --help
end
EOF
if [ -n "$help" ]; then printf "%s\n" "Usage: ${BASH_SOURCE[0]} [-g|--group userGroup]"; exit 0; fi
if [ -n "$group" ]; then group="$1"; else group="acake2php"; fi

function create_www_path() {
  mkdir -p "$1"
  chmod -Rv 1770 "$1"
  chown -Rv :"$group" "$1"
}
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
