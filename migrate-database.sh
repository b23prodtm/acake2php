#!/bin/bash
# HOWTO: connect to the database, a mysql56+ server
# must be reachable locally. If it's the 1st time you use this connection,
# Configure it by logging in with shell:$ mysql -u root
# Then execute this SQL statements : create database DATABASE_NAME; # NO QUOTES ''
# 				use database DATABASE_NAME;
#				create user 'DATABASE_USER'@'localhost' 
#				identified by 'DATABASE_PASSWORD';
# The values of CakePHP DB VARIABLES available at app/Config/database.php.
# Don't forget to grant all privileges to 'DATABASE_USER'@'localhost' e.g.:
# GRANT ALL ON phpcms.* to 'test'@'localhost' identified by 'mypassword';

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
./lib/Cake/Console/cake schema update --file myschema.php

