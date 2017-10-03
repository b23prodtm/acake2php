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
export CAKEPHP_DEBUG_LEVEL=2

echo "WELCOME HOMEPAGE http://localhost:8080/pages/home-redirect?local=1"
lib/Cake/Console/cake server -p 8080 $*
