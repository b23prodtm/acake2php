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
#; this is generated for the development phase, don't use the same values for production !
#;
#;
export GET_HASH_PASSWORD="saINNH2X5e87I"
#;
#;
#; this development phase passwords, don't use the same values for production !
#;
#;
export CAKEPHP_DEBUG_LEVEL=2

echo "WELCOME HOMEPAGE http://localhost:8080/pages/home?local=1"
lib/Cake/Console/cake server -p 8080 $*
