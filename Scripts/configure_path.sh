#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
function create_www_path() {
  mkdir -p "$1"
  chmod -Rv 1770 "$1"
  chown -Rv :www-data "$1"
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
