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
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
openshift=$(parse_arg_exists "-[oO]*|--openshift" $*)
if [[ $openshift -eq 1 ]]; then
  echo "Real environment bootargs..."
else
  echo "Provided local/test bootargs..."
  source ./Scripts/bootargs.sh $*
fi
echo -e "

${red}
        ///// MySQL HOWTO: connect to the database${nc}
        A MySQL server (must match remote server version)
        must be reachable locally. If it's the 1st time you use this connection,
        Configure it as a service and log in with super or admin user shell:${green}mysql -u root -p${nc}
        See common issues in README.md file.
        These SQL statements initializes the database, replaced with current ${orange}environment variables${nc} :
"
# Got passed args so we have saved them before $ source <script> <nullIsPassedArgs>
saved=("$*")
dbfile=database.cms.php
if [[ (-f app/Config/database.php) ]]; then
        echo -e "Defaults to app/Config/database.php ..."
fi
source ./Scripts/config_app_database.sh ${dbfile}
# Reset passed args (shift reset)
echo "Saved params :  set -- ${saved}"
set -- $saved
fix_db="-N"
update_checked=0
while [[ "$#" > 0 ]]; do case $1 in
  -[uU]* )
      update_checked=1
      fix_db="-Y"
      ;;
  -[yY]* )
      # set anything to validate, or none is equal to "-N"
      fix_db="-Y"
      ;;
  -[nN]* )
      fix_db="-N"
      dbfile=""
      ;;
  -[iI]* )
      fix_db="-Y"
      identities=app/Config/identities.sql
      if [[ -f $identities ]]; then ./Scripts/cp_bkp_old.sh . $identities ${identities}.old; fi
      echo -e "
                create database if not exists ${DATABASE_NAME};\r
                use mysql;\r
                create user if not exists '${DATABASE_USER}'@'${MYSQL_SERVICE_HOST}';\r
                alter user '${DATABASE_USER}'@'${MYSQL_SERVICE_HOST}' identified by '\${DATABASE_PASSWORD}';\r
                select * from user where user = '${DATABASE_USER}';\r
                grant all on ${DATABASE_NAME}.* to '${DATABASE_USER}'@'${MYSQL_SERVICE_HOST}';\r

                create database if not exists ${TEST_DATABASE_NAME};\r
                use mysql;\r
                create user if not exists '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}';\r
                alter user '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}' identified by '\${TEST_DATABASE_PASSWORD}';\r
                select * from user where user = '${TEST_DATABASE_USER}';\r
                grant all on ${TEST_DATABASE_NAME}.* to '${TEST_DATABASE_USER}'@'${TEST_MYSQL_SERVICE_HOST}';\r
      " > $identities
      cat $identities
      ;;
  -[hH]*|--help )
    echo "Usage: $0 [-u] [-y|n] [-i] [-o] [-p|--sql-password=<password>] [--test-sql-password]
        -u
            Update the database in app/Config/Schema/
        -y
            Reset ${dbfile} and default socket file
        -n
            Doesn't reset ${dbfile} and socket
        -i
            Import SQL identities
        -o, --openshift
            Resets ${dbfile}, keep socket and update the database (should be used with -i)
        -p, --sql-password=<password>
            Exports DATABASE_PASSWORD
        --test-sql-password=<password>
            Exports TEST_DATABASE_PASSWORD
        -h, --help
            Displays this help"
        exit 0;;
  -[oO]*|--openshift )
    fix_db="-N"
    update_checked=1;;
  -[pP]*|--sql-password*)
    export DATABASE_PASSWORD=$(parse_sql_password "$1" "$DATABASE_USER");;
  --test-sql-password*)
    export TEST_DATABASE_PASSWORD=$(parse_sql_password "$1" "$TEST_DATABASE_USER");;
  *) echo "Unknown parameter passed: $1"; exit 1;;
  esac
shift; done
if [ -f app/Config/$dbfile ]; then
        echo -e "Reset to ${dbfile} settings and default socket file..."
fi
shell_prompt "./Scripts/config_app_database.sh ${dbfile} ${fix_db}" "${cyan}Setup connection and socket\n${nc}" $fix_db
if [ -f $identities ]; then
  echo -e "Importing the mysql ${cyan}${DATABASE_USER}${nc} and ${cyan}${TEST_DATABASE_USER}${nc} users SQL identities..."
  echo "source ${identities}" | mysql -u $DATABASE_USER --password=$DATABASE_PASSWORD
fi
if [ $update_checked == 1 ]; then
  #; update plugins and dependencies
  source ./Scripts/composer.sh
  #; cakephp shell
  if [ ! -f app/Config/Schema/schema.php ]; then
    echo "Generating database schema 'cake schema generate'"
    ./lib/Cake/Console/cake schema generate -y
  fi
  if [ ! -f app/Config/Schema/sessions.php ]; then
      echo "Generating default Sessions table"
      ./lib/Cake/Console/cake schema create Sessions -y
  fi
  echo "Migrating database 'cake schema update' ..."
  ./lib/Cake/Console/cake schema update --file myschema.php -y
fi
