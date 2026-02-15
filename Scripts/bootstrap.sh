#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" && pwd)
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
# shellcheck source=lib/shell_prompt.sh
. "${TOPDIR}/Scripts/lib/shell_prompt.sh"
parse_args "$@" <<EOF
flag runner -r --runner
end
EOF
log_daemon_msg "Auto configuration..."
#; hash file that is stored in webroot to allow administrator privileges
if [ -z "${MASTER_PASSWORD_HASH:-}" ] && [ -z "$runner" ]; then
  hash="$TOPDIR/${MYPHPCMS_DIR}/e13/etc/export_hash_password.sh"
  while [ ! -f "$hash" ]; do
    shell_prompt "$TOPDIR/Scripts/config_etc_pass.sh" "define a value for missing MASTER_PASSWORD_HASH" "${DEBIAN_FRONTEND:-}"
  done
  # shellcheck source=app/webroot/php-cms/e13/etc/export_hash_password.sh
  . "$hash"
fi
# shellcheck disable=SC2154
echo -e "${nc}Password ${green}${MASTER_PASSWORD_HASH}${nc}"
#; Install PHPUnit, performs unit tests
#; The website must pass health checks in order to be deployed
if [ -n "$runner" ]; then
  phpunit="$TOPDIR/app/Vendor/bin/phpunit"
  if [ ! -f "$phpunit" ]; then
    # shellcheck source=composer.sh
    "${TOPDIR}/Scripts/composer.sh" install --dev --no-interaction --ignore-platform-reqs
  else
   log_daemon_msg "PHPUnit ${green}[OK]${nc}"
  fi
  printf "%s\n" "$($phpunit --version)"
  set -- "--runner" "$@"
fi
bash -c "$TOPDIR/Scripts/start_daemon.sh $@"
