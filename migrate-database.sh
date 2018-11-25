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

${red}
        ///// MySQL HOWTO: connect to the database${nc}
        A MySQL server (must match remote server version)
        must be reachable locally. If it's the 1st time you use this connection,
        Configure it as a service and log in with super or admin user shell:${green}mysql -u root${nc}
        These SQL statements initializes the database, replaced with ${orange}environment variables${nc} :
"
identities=app/Config/identities.sql
if [[ -f $identities ]]; then ./Scripts/cp_bkp_old.sh . $identities ${identities}.old; fi
echo -e "
          create database if not exists ${TEST_DATABASE_NAME};\r
          use mysql;\r
          create user if not exists '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}';\r
          alter user '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}' identified by '${TEST_DATABASE_PASSWORD}';\r
          select * from user where user = '${TEST_DATABASE_USER}';\r
          grant all on ${TEST_DATABASE_NAME}.* to '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}';\r
" > $identities
cat $identities
echo -e "
         The values of CakePHP DB VARIABLES available at ${cyan}app/Config/database.php${nc}.
         Don't forget to grant all privileges.
         Type in shell to login ${green}mysqld ${nc}local daemon as above should give the following results :
${cyan}
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

        //// FAQ:
        1. How to fix the following error ?
            ${orange}
            errno : 1146
            sqlstate : 42S02
            error : Table 'phpcms.info' doesn't exist${nc}\n
          Try the following to migrate (update) all database tables, answer 'y' when prompted:
            ${cyan}
            ./migrate-database.sh -u${nc}\n
        2a. ${orange}ACCESS DENIED for user $TEST_DATABASE_USER appears with other information complaining about database connection, what does that mean ?
          You probably have modified user privileges on your server:
            ${cyan}
            mysql -u root
            use mysql;
            grant all on \$TEST_DATABASE_NAME.* to '\$TEST_DATABASE_USER'@'\$TEST_MYSQL_SERVICE_HOST';
            exit
            ./configure.sh -c${nc}\n
          This will reset the connection profile in ..etc/ properties file.
        2b. ${orange}ACCESS DENIED for root@'$TEST_MYSQL_SERVICE_HOST' appears with other information complaining about database connection, what does that mean ?
          This looks like a first installation of mysql. You have to secure or reset your mysql root access:
            ${cyan}
            sudo rm -rf /usr/local/var/mysql
            mysqld --initialize${nc}\n
          [Note] A temporary password is generated for root@localhost. Now import identities.
            ${cyan}
            brew services restart mysql@${sqlversion}
            ./migrate-database.sh -Y -i
            <temporary password>
            ${nc}\n

        3. My mysql server's upgraded to another version, what should I do ?
          Upgrade your phpcms database within a (secure)shell:
            ${cyan}
            mysql_upgrade -u root${nc}\n
        4. I've made changes to mysql database tables, I've made changes to Config/Schema/myschema.php, as Config/database.php defines it, what should I do ?
          Migrate all your tables:
            ${cyan}
            ./migrate-database.sh -u${nc}\n
          Answer 'y' when prompted.
        5. How to fix up ${orange}'Database connection \"Mysql\" is missing, or could not be created'${nc} ?
					Check your environment variable (./Scripts/bootargs.sh or Docker/Pod settings)
            ${cyan}TEST_DATABASE_NAME=$TEST_DATABASE_NAME${nc}
     			Log in with authorized privileges from a shell prompt:
					${cyan}
						mysql -u root
						use $TEST_DATABASE_NAME${nc}\n
     		6. How to fix up ${orange}ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/mysql/mysql.sock' (2)${nc} ?
					Run the socket fixup script with arguments:
						${cyan}
      			./migrate-database.sh -y
      			brew services restart mysql@${sqlversion}${nc}\n"
# Got passed args so we have saved them before $ source <script> <nullIsPassedArgs>
saved=("$@")
source ./Scripts/config_app_database.sh
# Reset passed args (shift reset)
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
      fix_db="-Y"
      ;;
  -[yY]* )
      # set anything to validate, or none is equal to "-N"
      fix_db="-Y"
      ;;
  -[nN]* )
      fix_db="-N"
      ;;
  -[iI]* )
      echo "Please, enter the mysql root password to import test/local identities..."
      echo "source ${identities}" | mysql -u root -p
      fix_db="-N";;
  -[hH]*|--help )
    echo "./migrate-database.sh [-uy|n]
        -u Update database in app/Config/Schema/
        -y Reset settings and default socket file
        -n Doesn't do anything
        -i Import identities
        "
        exit 0;;
  *);;
esac; shift; done
dbfile=database.cms.php
if [ -f app/Config/$dbfile ]; then
      	echo -e "Reset to ${dbfile} settings and default socket file..."
      	source ./Scripts/shell_prompt.sh "./Scripts/config_app_database.sh ${dbfile} ${fix_db}" "${cyan}Setup connection and socket\n${nc}" $fix_db
fi
