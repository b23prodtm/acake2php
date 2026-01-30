#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" && pwd)
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
parse_args_lazy "$@" <<EOF
flag runner -r --runner
end
EOF
log_progress_msg "Boot..."
#; Install PHPUnit, performs unit tests
#; The website must pass health checks in order to be deployed
if [ ${#runner} -gt 0 ]; then
  phpunit="$TOPDIR/app/Vendor/bin/phpunit"
  if [ ! -f "$phpunit" ]; then
    # shellcheck source=composer.sh
    . "${TOPDIR}/Scripts/composer.sh" install --dev --no-interaction --ignore-platform-reqs
  else
   log_success_msg "PHPUnit [OK]"
  fi
  printf "%s\n" "$($phpunit --version)"
  set -- "--runner" "$@"
fi
. "$TOPDIR/Scripts/start_daemon.sh" "$@"
