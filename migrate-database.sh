#!/bin/bash
set -e 

if [ ! -f app/Config/Schema/schema.php ]; then
	echo "Generating database schema 'cake schema generate' ..."
	./lib/Cake/Console/cake schema generate
fi

echo "Migrating database 'cake schema create' ..."
./lib/Cake/Console/cake schema create
