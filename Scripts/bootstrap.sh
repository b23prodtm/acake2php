#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" && pwd)
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
# shellcheck source=lib/shell_prompt.sh
. "${TOPDIR}/Scripts/lib/shell_prompt.sh"
parse_args_lazy "$@" <<EOF
flag runner -r --runner
end
EOF
log_progress_msg "Auto configuration..."
log_debug "$(log_progress_msg "hash file that is stored in webroot to allow administrator privileges")"
if [ -z "${MASTER_PASSWORD_HASH:-}" ] && [ -z "$runner" ]; then
  hash="${TOPDIR}/master_password_hash"
  while [ ! -f "$hash" ]; do
    shell_prompt "$TOPDIR/Scripts/config_etc_pass.sh -f $hash" "define a value for missing MASTER_PASSWORD_HASH" "${DEBIAN_FRONTEND:-}"
  done
  MASTER_PASSWORD_HASH="$(cat "$hash")"
  export MASTER_PASSWORD_HASH
fi
# shellcheck disable=SC2154
log_debug "$(log_progress_msg "${nc}Password ${green}${MASTER_PASSWORD_HASH}${nc}")"
#; Install PHPUnit, performs unit tests
#; The website must pass health checks in order to be deployed
if [ -n "$runner" ]; then
  phpunit="$TOPDIR/app/Vendor/bin/phpunit"
  if [ ! -f "$phpunit" ]; then
    # shellcheck source=composer.sh
    "${TOPDIR}/Scripts/composer.sh" install --dev --no-interaction --ignore-platform-reqs
  else
   log_progress_msg "PHPUnit ${green}[OK]${nc}"
  fi
  printf "%s\n" "$($phpunit --version)"
  set -- "--runner" "$@"
fi
bash -c "$TOPDIR/Scripts/start_daemon.sh $*"
