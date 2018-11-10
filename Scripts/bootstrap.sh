#!/bin/sh
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
        source ./Scripts/shell_prompt.sh "./configure.sh -Y -N -N" "configuration"
fi
#;
#;
#; hash file that is stored in webroot to allow administrator privileges
#;
#;
echo "Configuration begins automatically...${green}"
hash="${PHP_CMS_DIR}/e13/etc/export_hash_password.sh"
if [ ! -f $hash ]; then
        source ./Scripts/shell_prompt.sh "./configure.sh -N -Y -N" "configuration"
fi
source $hash
echo "${nc}Password ${green}$GET_HASH_PASSWORD${nc}"
#; update plugins and dependencies
source ./Scripts/composer.sh
#;
#;
#; PHPUnit performs unit tests
#; The website must pass health checks in order to be deployed
#;
#;
phpunit="vendors/bin/phpunit"
if [ ! -f $phpunit ]; then
        echo "Composer will download the PHPUnit framework"
        version=3
        PHPCS=3
#        CakePHP 2.X compatible with PHPUnit 3.7
#        PHPUnit 4+ needs CakePHP 3+.
        if [ `expr "\`php --version\`" : 'PHP\ 5\.[0-3]\.'` -gt 0 ]; then
                version=3
                PHPCS=1
        fi
#        if [ `expr "\`php --version\`" : 'PHP\ 5\.[4-9]\.'` -gt 0 ]; then
#                version=3
#                PHPCS=3
#        fi
#        if [ `expr "\`php --version\`" : 'PHP\ 7\.0\.'` -gt 0 ]; then
#                version=3
#                PHPCS=3
#        fi
#        if [ `expr "\`php --version\`" : 'PHP\ 7\.[1-9]\.'` -gt 0 ]; then
#                version=3
#                PHPCS=3
#        fi
        echo " version $version...\n"
        if [ ! -f bin/composer.phar ]; then
                source ./Scripts/composer.sh
        fi
        php bin/composer.phar require --prefer-dist --update-with-dependencies --dev phpunit/phpunit ^$version cakephp/cakephp-codesniffer ^$PHPCS
else
        echo "PHPUnit ${green}[OK]${nc}"
fi
echo `$phpunit --version`
