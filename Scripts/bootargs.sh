#!/bin/sh
set -e
#; colorize shell script
nc="\033[0m"
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
#; Host name (unix) 'localhost' generally replaces '127.0.0.1' (macOS).
export DATABASE_ENGINE="Mysql"
export DATABASE_SERVICE_NAME="MYSQL"
#;export MYSQL_SERVICE_HOST="127.0.0.1"
export MYSQL_SERVICE_HOST="localhost"
export MYSQL_SERVICE_PORT="3306"
export DATABASE_NAME="phpcms"
export DATABASE_USER="test"
export DATABASE_PASSWORD="mypassword"
#. Test configuration ?test=1
export TEST_MYSQL_SERVICE_HOST="127.0.0.1"
#;export TEST_MYSQL_SERVICE_HOST="localhost"
export TEST_MYSQL_SERVICE_PORT="3306"
export TEST_DATABASE_NAME="cakephp_test"
export TEST_DATABASE_USER="root"
export TEST_DATABASE_PASSWORD=${SQL_PASSWORD}
export FTP_SERVICE_HOST="localhost"
export FTP_SERVICE_USER="test"
export FTP_SERVICE_PASSWORD="mypassword"
export PHP_CMS_DIR="./app/webroot/php_cms/"
#; More about default environment app/Config/core.php
#; Openshift Online secure keys (default_keys)
export CAKEPHP_SECURITY_SALT="Word"
export CAKEPHP_SECURITY_CIPHER_SEED="01234"
#; 0, 1, 2 the higher the more debug data
export CAKEPHP_DEBUG_LEVEL=2
