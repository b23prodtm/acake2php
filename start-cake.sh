#!/bin/sh
#;
#;
#; this is configuration for development phase and runtime
#;
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
if [ ! -f app/webroot/php-cms/e13/etc/constantes_local.properties ]; then
        echo "PLEASE RUN ./CONFIGURE.SH FIRST !"
        exit
fi
#;
#;
#; this is generated for the development phase, don't use the same values for production !
#;
#;
hash="app/webroot/php-cms/e13/etc/export_hash_password.sh"
if [ ! -f $hash ]; then
        echo "PLEASE RUN ./CONFIGURE.SH FIRST !"
        exit
fi
source $hash
echo "================================"
echo "PASSWORD HASH $GET_HASH_PASSWORD"
echo "WELCOME HOMEPAGE IS http://localhost:8080"
echo "================================"
lib/Cake/Console/cake server -p 8080 $*
