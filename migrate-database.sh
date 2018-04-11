#!/bin/bash
#;
#; Pass as arguments values "-y -y -y" to override user prompt, if you're in "nutshell".
#; ./migrate_database.sh $1 $2 $3
#; see below commmands shema generate $1 | create Sessions $2 | update --file myschema.php $3
#;
#;
set -e 

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

