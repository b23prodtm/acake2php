#!/usr/bin/env bash
set -e
source ./Scripts/lib/logging.sh
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
openshift=$(parse_arg_exists "-[oO]+|--openshift"  $*)
pargs=$(parse_arg_trim "-[oO]+|--openshift"  $*)
if [ $openshift 2> /dev/null ]; then
  slogger -st $0 "Bootargs...: ${pargs}"
  export CAKEPHP_DEBUG_LEVEL=1
  source ./Scripts/bootargs.sh $*
else
  slogger -st $0 "Locally Testing values, bootargs...: ${pargs}"
  export CAKEPHP_DEBUG_LEVEL=2
  source ./Scripts/fooargs.sh $*
fi
#;
#; check if file etc/constantes_local.properties exist (~ ./configure.sh was run once)
#;
if [ ! -f ./$PHP_CMS_DIR/e13/etc/constantes.properties ] && [ ! $openshift 2> /dev/null ]; then
  shell_prompt "./configure.sh -c" "missing file creation constantes.properties"
fi
slogger -st $0 "Auto configuration..."
#; hash file that is stored in webroot to allow administrator privileges
if [[ -z ${GET_HASH_PASSWORD:-''} ]] && [ ! $openshift 2> /dev/null ]; then
  hash="./${PHP_CMS_DIR}/e13/etc/export_hash_password.sh"
  if [ ! -f $hash ]; then
    shell_prompt "./configure.sh -h " "define a value for missing GET_HASH_PASSWORD"
  fi
  source $hash
fi
echo -e "${nc}Password ${green}${GET_HASH_PASSWORD}${nc}"
#; Install PHPUnit, performs unit tests
#; The website must pass health checks in order to be deployed
if [ $openshift 2> /dev/null ]; then
  phpunit="./app/Vendor/bin/phpunit"
  if [ ! -f $phpunit ]; then
    source ./Scripts/composer.sh install --dev --no-interaction
  else
   slogger -st $0 "PHPUnit ${green}[OK]${nc}"
  fi
  echo `$phpunit --version`
fi
bash -c "./Scripts/start_daemon.sh ${pargs}"
