#!/bin/sh
#;
#;
#; this is configuration for development phase and runtime
#;
#;
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
#; 
export DATABASE_ENGINE="mysql"
export DATABASE_SERVICE_NAME="mysql"
export TEST_MYSQL_SERVICE_HOST="localhost"
export TEST_MYSQL_SERVICE_PORT="3306"
export TEST_DATABASE_NAME="phpcms"
export TEST_DATABASE_USER="test"
export TEST_DATABASE_PASSWORD="mypassword"
export FTP_SERVICE_HOST="local"
export FTP_SERVICE_USER="test"
export FTP_SERVICE_PASSWORD="mypassword"
#;
#;
#; this development phase, don't use the same values for production (no setting means no debugger)!
#;
#;
export CAKEPHP_DEBUG_LEVEL=2
#;
#; check if file etc/constantes_local.properties exist (~ ./configure.sh was run once)
#;
if [ ! -f app/webroot/php-cms/e13/etc/constantes.properties ]; then
        echo "${red}PLEASE RUN ./CONFIGURE.SH FIRST !${nc}"
        exit
fi
#;
#;
#; hash file that is stored in webroot to allow administrator privileges
#;
#;
echo "Configuration begins...${green}"
hash="app/webroot/php-cms/e13/etc/export_hash_password.sh"
if [ ! -f $hash ]; then
        echo "${red}PLEASE RUN ./CONFIGURE.SH FIRST !${nc}"
        exit
fi
source $hash
echo "${nc}Password ${green}$GET_HASH_PASSWORD${nc}"
#;
#;
#; Composer simplifies the process to add features like plugins
#; 
#;
composer="bin/composer.phar"
if [ ! -f $composer ]; then
        echo "Composer setup...\n"
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
        php composer-setup.php --install-dir=bin
        php -r "unlink('composer-setup.php');"
else
        echo "Composer ${green}[OK]${nc}"
fi
echo `bin/composer.phar --version`
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
#        CakePHP 2.X compatible with PHPUnit 3.7 
#        PHPUnit 4+ needs CakePHP 3+.
#        if [ `expr "\`php --version\`" : 'PHP\ 5\.5\.'` -gt 0 ]; then
#                version="4"
#        fi
#        if [ `expr "\`php --version\`" : 'PHP\ 5\.[6-9]\.'` -gt 0 ]; then
#                version="5"
#        fi   
#        if [ `expr "\`php --version\`" : 'PHP\ 7\.0\.'` -gt 0 ]; then
#                version="6"
#        fi     
#        if [ `expr "\`php --version\`" : 'PHP\ 7\.[1-9]\.'` -gt 0 ]; then
#                version="7"
#        fi     
        echo " version $version...\n"
        php bin/composer.phar require --prefer-dist --update-with-dependencies --dev phpunit/phpunit ^$version 
else
        echo "PHPUnit ${green}[OK]${nc}"
fi
echo `$phpunit --version`
echo "Welcome homepage ${cyan}http://localhost:8080${nc}"
echo "Debugging echoes ${cyan}/admin/index.php${green}?debug=1&verbose=1${nc}"
echo "Alternate local tests ${cyan}/admin/index.php${green}?local=1${nc}"
echo "Turnoff flags ${cyan}/admin/logoff.php${nc}"
echo "==============================================="
lib/Cake/Console/cake server -p 8080 $*
