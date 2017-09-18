#!/bin/sh
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
export GET_HASH_PASSWORD="saINNH2X5e87I"
# local default configuration deployment
cp app/webroot/php-cms/e13/etc/constantes_template.properties app/webroot/php-cms/e13/etc/constantes_local.properties

# Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Disable automatic image stream deployment.
# Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.
# Scale down to zero, start build and deploy when finished to build. Then scale up to your pod usage needs.

lib/Cake/Console/cake server -p 2233
