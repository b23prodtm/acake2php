#!/bin/bash
set -e
source ./Scripts/bootargs.sh
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
        source ./Scripts/shell_prompt.sh "./configure.sh -c" "configuration"
fi
#;
#;
#; hash file that is stored in webroot to allow administrator privileges
#;
#;
echo "Configuration begins automatically...${green}"
hash="${PHP_CMS_DIR}/e13/etc/export_hash_password.sh"
if [ ! -f $hash ]; then
        source ./Scripts/shell_prompt.sh "./configure.sh -c -h" "configuration"
fi
source $hash
echo -e "${nc}Password ${green}$GET_HASH_PASSWORD${nc}"
#; update plugins and dependencies
source ./Scripts/composer.sh
#; Install PHPUnit, performs unit tests
#; The website must pass health checks in order to be deployed
phpunit="app/vendor/bin/phpunit"
if [ ! -f $phpunit ]; then
        if [ ! -f bin/composer.phar ]; then
                source ./Scripts/composer.sh
        fi
        php bin/composer.phar require --prefer-dist --update-with-dependencies --dev phpunit/phpunit ^$version cakephp/cakephp-codesniffer ^$PHPCS
else
        echo -e "PHPUnit ${green}[OK]${nc}"
fi
echo `$phpunit --version`
