#!/bin/bash

export DATABASE_ENGINE="mysql"
export DATABASE_SERVICE_NAME="mysql"
export TEST_MYSQL_SERVICE_HOST="localhost"
export TEST_MYSQL_SERVICE_PORT="3306"
export TEST_DATABASE_NAME="phpcms"
export TEST_DATABASE_USER="test"
export TEST_DATABASE_PASSWORD="mypassword"

set -e 

if [ ! -f app/Config/Schema/schema.php ]; then
	echo "Generating database schema 'cake schema generate' ..."
	./lib/Cake/Console/cake schema generate
fi

echo "Migrating database 'cake schema create' ..."
./lib/Cake/Console/cake schema create
echo "Building default Sessions table..."
./lib/Cake/Console/cake schema create Sessions
