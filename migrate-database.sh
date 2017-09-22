#!/bin/bash
set -e 

if [ ! -f app/Config/Schema/schema.php ]; then
	echo "Generating database schema 'cake schema generate' ..."
	./lib/Cake/Console/cake schema generate
fi
if [ ! -f app/Config/Schema/sessions.php ]; then
        echo "Generating default Sessions table..."
        ./lib/Cake/Console/cake schema create Sessions
fi
echo "Migrating database 'cake schema create' ..."
./lib/Cake/Console/cake schema update --file schema.php
echo "Update default Sessions table..."
./lib/Cake/Console/cake schema update --name Sessions

