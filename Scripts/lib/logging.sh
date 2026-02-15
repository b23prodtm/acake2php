#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
parse_args "$@" <<EOF
flag runner -r --runner
end
EOF
# ---------------------------------------------------------------------------
# PUBLIC API (backward compatible)
# ---------------------------------------------------------------------------

function log_msg_daemon() {
  log_daemon_msg "$*"
}
function log_msg_progress() {
  log_progress_msg "$*"
}
function log_msg_success() {
  log_success_msg "$*"
}
function log_msg_failure() {
  log_failure_msg "$*"
}

# Debug logging
function debug() {
  log_debug "$*"
}

if [ -n "$runner" ]; then
  export CAKEPHP_DEBUG_LEVEL=1
else
  export CAKEPHP_DEBUG_LEVEL=2
fi
# ---------------------------------------------------------------------------
# LOG FILE SUPPORT (optional)
# ---------------------------------------------------------------------------

#; Make logs folders available
mkdir -p "${MYPHPCMS_LOG}"
function new_cake_log() {
  a="$(new_log)"
  ln -sf "$a" "$(basename "$a")"
}
