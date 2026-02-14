#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" && pwd)
incBOOT_ARGS=${incBOOT_ARGS:-0}; if [ "$incBOOT_ARGS" -eq 0 ]; then
  export incBOOT_ARGS=1
  log_daemon_msg "PRODUCTION MODE $0...: $*"
  [[ ! -e "$TOPDIR/.env" || ! -e "$TOPDIR/common.env" ]] \
  && printf "Missing environment configuration files.\n" && exit 1
  eval "$(cat "$TOPDIR/.env" "$TOPDIR/common.env" | awk 'BEGIN{ FS="\n" }{ print "export " $1 }')"
fi
