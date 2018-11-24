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
#; Pass as arguments values "[-y|n|u]" to override user prompt, if you're in "nutshell".
#; y fixup socket
#; u fixup socket and update schema (must connect successsfully)
#;
set -e
source ./Scripts/bootargs.sh
echo -e "

${red}                ///// MySQL HOWTO: connect to the database${nc}

 A MySQL@5.6 server (must match remote server version)
 must be reachable locally. If it's the 1st time you use this connection,
 Configure it as a service and log in with super or admin user shell:${green}mysql -u root${nc}
 These SQL statements initializes the database, replaced with ${orange}environment variables${nc} :

        create database ${orange}${TEST_DATABASE_NAME}${nc};
        use mysql;
        create user '${cyan}${TEST_DATABASE_USER}${nc}'@'${TEST_MYSQL_SERVICE_HOST}';
        alter user '${cyan}${TEST_DATABASE_USER}${nc}'@'${TEST_MYSQL_SERVICE_HOST}' identified by '${orange}${TEST_DATABASE_PASSWORD}${nc}';
        select * from user where user = '${cyan}${TEST_DATABASE_USER}${nc}';
        ${orange}grant all${nc} on ${TEST_DATABASE_NAME}.* to '${cyan}${TEST_DATABASE_USER}${nc}'@'${TEST_MYSQL_SERVICE_HOST}';

${nc}
 The values of CakePHP DB VARIABLES available at ${cyan}app/Config/database.php${nc}.
 Don't forget to grant all privileges.
 Type in shell to login ${green}mysqld ${nc}local daemon as above should give the following results :
${orange}
        mysql -u root
        create database \$TEST_DATABASE_NAME;
        use mysql;
        create user '\$TEST_DATABASE_USER'@'\$TEST_MYSQL_SERVICE_HOST';
        ${green}
        > Query OK, 0 row affected, ...
        ${orange}
        alter user '\$TEST_DATABASE_USER'@'127.0.0.1' identified by '\$TEST_DATABASE_PASSWORD';
        ${green}
        > Query OK, 0 row affected, ...
        ${orange}
        grant all on \$TEST_DATABASE_NAME.* to '\$TEST_DATABASE_USER'@'\$TEST_MYSQL_SERVICE_HOST';
        ${green}
        > Query OK, 0 row affected, ...
        ${nc}

${red}                        ///// FAQ${nc} :

                                        1.
        errno : 1146
        sqlstate : 42S02
        error : Table 'phpcms.info' doesn't exist

Run again ${green}./migrate-database.sh${nc}, to create or update database tables.

                                        2.
If ACCESS DENIED appears, please verify the user name and localhost values then
${cyan}
        grant all on phpcms.* to this user as above.
${nc}

                                        3.
${green}Whenever mysql server changes to another version${nc}, try an upgrade of phpcms database within a (secure)shell ${green}mysql_upgrade -u root${nc}

                                        4.
${green}Make changes to SQL database structure (table-models)${nc}, by modifying Config/Schema/myschema.php, as Config/database.php defines it.
Run ${green}./migrate-database.sh${nc}, answer ${cyan}Y${nc}es when prompted, which may not display any ${red}SQLSTATE [error]${nc}.
"
saved=("$@")
source ./Scripts/config_app_database.sh
echo 'set -- ${saved}'
set -- $saved
fix_db=$1
while [[ "$#" > 0 ]]; do case $1 in
  -[uU]* )
      if [ ! -f app/Config/Schema/schema.php ]; then
        echo "Generating database schema 'cake schema generate'"
        ./lib/Cake/Console/cake schema generate
      fi
      if [ ! -f app/Config/Schema/sessions.php ]; then
          echo "Generating default Sessions table"
          ./lib/Cake/Console/cake schema create Sessions
      fi
      echo "Migrating database 'cake schema update' ..."
      ./lib/Cake/Console/cake schema update --file myschema.php
      ;;
  -[yYuU]* )
      # set anything to validate, or none is equal to "-N"
      fix_db="-Y"
      ;;
  -[nN]* )      
      fix_db="-N"
      ;;
  -[hH]*|--help )
    echo "./migrate-database.sh [-uy|n]
        -u Update database in app/Config/Schema/
        -y Reset settings and default socket file
        -n Doesn't do anything
        "
        exit 0;;
  *);;
esac; shift; done
dbfile=database.cms.php
if [ -f app/Config/$dbfile ]; then
      	echo -e "Reset to ${dbfile} settings and default socket file..."
      	source ./Scripts/shell_prompt.sh "./Scripts/config_app_database.sh ${dbfile}" "${cyan}Setup connection and socket\n${nc}" $fix_db
fi