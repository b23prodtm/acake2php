#!/bin/bash
set -e
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
openshift=$(parse_arg_exists "-[oO]*|--openshift" $*)
if [ $openshift > /dev/null ]; then
  echo "Real environment bootargs..."
else
  echo "Provided local/test bootargs..."
  source ./Scripts/bootargs.sh $*
fi
#;
#;
#; this development phase, don't use the same values for production (no setting means no debugger)!
#;
#;
export CAKEPHP_DEBUG_LEVEL=2
#;
#; check if file etc/constantes_local.properties exist (~ ./configure.sh was run once)
#;
if [ ! -f ${PHP_CMS_DIR}/e13/etc/constantes.properties ]; then
        shell_prompt "./configure.sh -c" "configuration"
fi
echo "Configuration begins automatically...${green}"
#; hash file that is stored in webroot to allow administrator privileges
hash="${PHP_CMS_DIR}/e13/etc/export_hash_password.sh"
if [ ! -f $hash ]; then
        shell_prompt "./configure.sh -c -h" "configuration"
fi
source $hash
echo -e "${nc}Password ${green}$GET_HASH_PASSWORD${nc}"
#; Install PHPUnit, performs unit tests
#; The website must pass health checks in order to be deployed
phpunit="./app/Vendor/bin/phpunit"
if [ ! -f $phpunit ]; then
        if [ ! -f bin/composer.phar ]; then
                source ./Scripts/composer.sh
        fi
        php bin/composer.phar update --prefer-dist --with-dependencies phpunit/phpunit cakephp/cakephp-codesniffer
else
        echo -e "PHPUnit ${green}[OK]${nc}"
fi
echo `$phpunit --version`
source ./Scripts/config_app_database.sh
