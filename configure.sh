#!/bin/sh
export DATABASE_SERVICE_NAME="mysql"
export MYSQL_SERVICE_HOST="localhost"
export DATABASE_NAME="phpcms"
export MYSQL_SERVICE_PORT="3306"
export DATABASE_USER="test"
export DATABASE_PASSWORD="mypassword"
# local configuration deployment
 cp app/webroot/php-cms/e13/etc/constantes_template.properties app/webroot/php-cms/e13/etc/constantes_local.properties

# The above exported variables are locally made for development, and should differ when in production deployment.
# Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image.
# Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get killed.

