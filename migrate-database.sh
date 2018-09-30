#!/bin/bash
# HOWTO: connect to the database, a mysql56+ server (must match remote server version)
# must be reachable locally. If it's the 1st time you use this connection,
# Configure it by logging in with shell:$ mysql -u root
# Then execute this SQL statements : create database DATABASE_NAME; # NO QUOTES ''
# 				use database DATABASE_NAME;
#				create user 'DATABASE_USER'@'localhost'
#				identified by 'DATABASE_PASSWORD';
# The values of CakePHP DB VARIABLES available at app/Config/database.php.
# Don't forget to grant all privileges to 'DATABASE_USER'@'localhost' e.g.:
# GRANT ALL ON phpcms.* to 'test'@'localhost' identified by 'mypassword';
#;
#; Pass as arguments values "-y -y -y" to override user prompt, if you're in "nutshell".
#; ./migrate_database.sh $1 $2 $3
#; see below commmands shema generate $1 | create Sessions $2 | update --file myschema.php $3
#;
#;
set -e
dbfile=database.cms.php
if [ -f app/Config/$dbfile ]; then
	echo "Reset to $dbfile settings..."
	cp app/Config/$dbfile app/Config/database.php
fi
if [ ! -f app/Config/Schema/schema.php ]; then
	echo "Generating database schema 'cake schema generate' ..."
	./lib/Cake/Console/cake schema generate $1
fi
if [ ! -f app/Config/Schema/sessions.php ]; then
        echo "Generating default Sessions table..."
        ./lib/Cake/Console/cake schema create Sessions $2
fi
echo "Migrating database 'cake schema create' ..."
./lib/Cake/Console/cake schema update --file myschema.php $3
