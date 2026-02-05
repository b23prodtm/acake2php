#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
runner=$(parse_arg "-[rR]+|--runner"  "$@")
pargs=$(parse_arg_trim "-[rR]+|--runner"  "$@")
if [ -n "$runner" ]; then
  log_daemon_msg "Bootargs...: ${pargs}"
  export CAKEPHP_DEBUG_LEVEL=1
  # shellcheck source=bootargs.sh
  . "${TOPDIR}/Scripts/bootargs.sh" "$@"
else
  log_daemon_msg  "Locally Testing values, bootargs...: ${pargs}"
  export CAKEPHP_DEBUG_LEVEL=2
  # shellcheck source=fooargs.sh
  . "${TOPDIR}/Scripts/fooargs.sh" "$@"
fi
#; Make logs folders available
mkdir -p "${MYPHPCMS_LOG}"
function new_cake_log() {
  a="$(new_log)"
  ln -sf "$a" "$(basename "$a")"
}
