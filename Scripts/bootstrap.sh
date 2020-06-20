#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
# shellcheck source=lib/shell_prompt.sh
. "${TOPDIR}/Scripts/lib/shell_prompt.sh"
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
openshift=$(parse_arg_exists "-[oO]+|--openshift"  "$@")
pargs=$(parse_arg_trim "-[oO]+|--openshift"  "$@")
if [ -n "$openshift" ]; then
  slogger -st $0 "Bootargs...: ${pargs}"
  export CAKEPHP_DEBUG_LEVEL=1
  # shellcheck source=bootargs.sh
  . "${TOPDIR}/Scripts/bootargs.sh" "$@"
else
  slogger -st $0 "Locally Testing values, bootargs...: ${pargs}"
  export CAKEPHP_DEBUG_LEVEL=2
  # shellcheck source=fooargs.sh
  . "${TOPDIR}/Scripts/fooargs.sh" "$@"
fi
#;
#; check if file etc/constantes_local.properties exist (~ ./configure.sh was run once)
#;
if [ ! -f "$TOPDIR/$MYPHPCMS_DIR/e13/etc/constantes.properties" ] && [ -z "$openshift" ]; then
  shell_prompt "$TOPDIR/configure.sh -c" "missing file creation constantes.properties"
fi
slogger -st $0 "Auto configuration..."
#; hash file that is stored in webroot to allow administrator privileges
if [ -z ${GET_HASH_PASSWORD:-''} ] && [ -z "$openshift" ]; then
  hash="$TOPDIR/${MYPHPCMS_DIR}/e13/etc/export_hash_password.sh"
  if [ ! -f $hash ]; then
    shell_prompt "$TOPDIR/configure.sh -h " "define a value for missing GET_HASH_PASSWORD"
  fi
  # shellcheck source=app/webroot/php_cms/e13/etc/export_hash_password.sh
  . "$hash"
fi
# shellcheck disable=SC2154
echo -e "${nc}Password ${green}${GET_HASH_PASSWORD}${nc}"
#; Install PHPUnit, performs unit tests
#; The website must pass health checks in order to be deployed
if [ -n "$openshift" ]; then
  phpunit="$TOPDIR/app/Vendor/bin/phpunit"
  if [ ! -f $phpunit ]; then
    # shellcheck source=composer.sh
    "${TOPDIR}/Scripts/composer.sh" install --dev --no-interaction --ignore-platform-reqs
  else
   slogger -st $0 "PHPUnit ${green}[OK]${nc}"
  fi
  printf "%s\n" "$($phpunit --version)"
fi
bash -c "$TOPDIR/Scripts/start_daemon.sh ${pargs}"
