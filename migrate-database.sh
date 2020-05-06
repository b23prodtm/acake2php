#!/usr/bin/env bash
set -eu
source ./Scripts/lib/logging.sh
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
openshift=$(parse_arg_exists "-[oO]+|--openshift" "$@")
docker=$(parse_arg_exists "--docker" "$@")
pargs=$(parse_arg_trim "-[oO]+|--openshift|--docker" "$@")
if [ $openshift 2> /dev/null ]; then
  slogger -st $0 "Bootargs...: ${pargs}"
  source ./Scripts/bootargs.sh $*
else
  slogger -st $0 "Locally Testing values, bootargs...: ${pargs}"
  source ./Scripts/fooargs.sh $*
fi
LOG=$(new_log) && slogger -st $0 $LOG
usage=("" \
"Usage: $0 [-u] [-y|n] [-o] [-p <word>] [-t <word>] [-i] [--sql-password=<password>] [--test-sql-password=<password>]" \
"          -u          Update the database in app/Config/Schema/" \
"          -y          Reset database.php and default socket file" \
"          -n          Doesn't reset database.php and socket" \
"          -i --sql-password=<word> --test-sql-password=<word>" \
"                      Import SQL identities with new passwords and reset MYSQL_DATABASE and TEST_DATABASE_NAME privileges" \
"          -o, --openshift" \
"                      Resets database.php, keep socket and update the database" \
"          -p=<password>" \
"                      Exports MYSQL_ROOT_PASSWORD" \
"          -t=<password>" \
"                      Exports MYSQL_PASSWORD" \
"          --database=<name>" \
"                      Exports MYSQL_DATABASE" \
"          --testunitbase=<name>" \
"                      Exports TEST_DATABASE_NAME" \
"          --enable-authentication-plugin" \
"                      Disables https://mariadb.com/kb/en/authentication-plugin-ed25519/" \
"          -v, --verbose" \
"                      Outputs more debug information" \
"          -h, --help  Displays this help" \
"")
sql_connect="mysql"
sql_connect_host="-h ${MYSQL_HOST} -P ${MYSQL_TCP_PORT}"
sql_connect_test_host="-h ${MYSQL_HOST} -P ${MYSQL_TCP_PORT}"
dbfile=database.cms.php
fix_socket="-N"
config_app_checked="-Y"
test_checked=0
update_checked=0
import_identities=0
saved=("$@")
authentication_plugin=0
mysql_host="%"
ck_args="--connection=default"
MARIADB_SHORT_NAME=$(echo $SECONDARY_HUB | awk -F/ '{ print $2 }' | awk -F: '{ print $1 }')
while [[ "$#" > 0 ]]; do case "$1" in
  --enable-authentication-plugin*)
    slogger -st $0 "Enabled auth_ed25519 plugin..."
    authentication_plugin=1;;
  --docker )
    bash -c "./Scripts/start_daemon.sh ${docker}"
    sql_connect="docker exec $MARIADB_SHORT_NAME mysql"
    sql_connect_host=""
    sql_connect_test_host=""
    ;;
  -[uU]* )
    update_checked=1
    ;;
  --connection* )
    ck_args="$1"
    ;;
  -[yY]* ) fix_socket="-Y";;
  -[nN]* )
    fix_socket="-N"
    dbfile=""
    config_app_checked="-N";;
  -[iI]* )
    import_identities=1
    ;;
  --sql-password*)
    OPTIND=1
    parse_sql_password "set_DATABASE_PASSWORD" "Altering ${DATABASE_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  --test-sql-password*)
    OPTIND=1
    parse_sql_password "set_MYSQL_PASSWORD" "Altering ${MYSQL_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[vV]*|--verbose )
    # Reset passed args (shift reset)
    text=("" \
"Passed params : $0 ${saved[*]}" \
"and environment VARIABLES:" \
$(export -p | grep "DATABASE\|MYSQL") \
"")
    printf "%s\n" "${text[@]}"
    ;;
  -[hH]*|--help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[oO]*|--openshift );;
  -[pP]* )
    parse_sql_password "MYSQL_ROOT_PASSWORD" "current ${DATABASE_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[tT]* )
    test_checked=1
    printf "Testing %s Unit..." $test_checked
    parse_sql_password "MYSQL_PASSWORD" "current ${MYSQL_USER} password" "$@"
    shift $((OPTIND -1))
    ck_args="--connection=test"
    ;;
  --database*)
    # Transform long options to short ones
    arg=$1; shift; set -- $(echo "${arg}" \
    | awk 'BEGIN{ FS="[ =]+" }{ print "-d " $2 }') "$@"
    parse_and_export "d" "MYSQL_DATABASE" "${DATABASE_USER} database name" "$@"
    shift $((OPTIND -1))
    ;;
  --testunitbase*)
    # Transform long options to short ones
    arg=$1; shift; set -- $(echo "${arg}" \
    | awk 'BEGIN{ FS="[ =]+" }{ print "-u " $2 }') "$@"
    test_checked=1
    parse_and_export "u" "TEST_DATABASE_NAME" "${MYSQL_USER} database name" "$@"
    shift $((OPTIND -1))
    ;;
  *) echo "Invalid parameter: ${BASH_SOURCE[0]} $1" && exit 1;;
  esac
shift; #echo "$@";
done
#; check unbound variables, exits scripts and inform user on the standard output.
: ${MYSQL_DATABASE?} ${DATABASE_USER?} ${MYSQL_ROOT_PASSWORD?} ${MYSQL_TCP_PORT?}
: $TEST_DATABASE_NAME?} ${MYSQL_USER?} ${MYSQL_PASSWORD?} ${MYSQL_HOST?} ${MYSQL_TCP_PORT?}
# configure user application database and eventually alter user database access
shell_prompt "./Scripts/config_app_database.sh ${dbfile} ${fix_socket} ${docker}" "${cyan}Setup ${dbfile} connection and socket\n${nc}" "$config_app_checked"
if [[ $import_identities -eq 1 ]]; then
  #; ---------------------------------- set MYSQL_ROOT_PASSWORD
  export set_DATABASE_PASSWORD=${set_DATABASE_PASSWORD:-$MYSQL_ROOT_PASSWORD}
  slogger -st $0 "\r${red}WARNING: You will modify SQL ${DATABASE_USER} password !${nc}"
  prompt="-Y"
  if [ -z ${set_DATABASE_PASSWORD} ]; then
    slogger -st $0 "\r${orange}WARNING: Using blank password for ${DATABASE_USER} !!${nc}"
    prompt=""
  fi
  if [ $authentication_plugin = 1 ]; then
    identifiedby="IDENTIFIED VIA ed25519 USING PASSWORD('${set_DATABASE_PASSWORD}')"
  else
    identifiedby="identified by '${set_DATABASE_PASSWORD}'"
  fi
  args=(\
"-e \"use mysql;\"" \
"-e \"alter user '${DATABASE_USER}'@'${mysql_host}' ${identifiedby};\"" \
"-e \"grant all PRIVILEGES on *.* to '${DATABASE_USER}'@'${mysql_host}' WITH GRANT OPTION;\"" \
"-e \"create database if not exists ${MYSQL_DATABASE} default character set='utf8' default collate='utf8_bin';\"" \
"-e \"create database if not exists ${TEST_DATABASE_NAME};\"" \
"-e \"create database if not exists ${TEST_DATABASE_NAME}2;\"" \
"-e \"create database if not exists ${TEST_DATABASE_NAME}3;\"" \
# enable failed-login tracking, such that three consecutive incorrect passwords cause temporary account locking for two days: \
# "-e \"FAILED_LOGIN_ATTEMPTS 3 PASSWORD_LOCK_TIME 2;\"" \
"-e \"select plugin from user where user='${DATABASE_USER}';\"" \
"-e \"show databases;\"" \
"")
  slogger -st $0 "Forked script to keep hidden table user secrets..."
  password=""
  if [ ! -z ${MYSQL_ROOT_PASSWORD:-''} ]; then
    password="--password=${MYSQL_ROOT_PASSWORD}"
  fi
  shell_prompt "exec ${sql_connect} ${sql_connect_host} -u ${DATABASE_USER} ${password} \
  ${args[*]} >> $LOG 2>&1" "Import default identities" "$prompt"\
  && export MYSQL_ROOT_PASSWORD=${set_DATABASE_PASSWORD}
  #; ---------------------------------- set MYSQL_PASSWORD
  slogger -st $0 "\r${red}WARNING: You will modify SQL ${MYSQL_USER} password !${nc}"
  export set_MYSQL_PASSWORD=${set_MYSQL_PASSWORD:-$MYSQL_PASSWORD}
  if [ -z ${set_MYSQL_PASSWORD} ]; then
    slogger -st $0 "\r${orange}WARNING: Using blank password for ${MYSQL_USER} !!${nc}"
    prompt=""
  fi
  if [ $authentication_plugin = 1 ]; then
    identifiedby="IDENTIFIED VIA ed25519 USING PASSWORD('${set_MYSQL_PASSWORD}')"
  else
    identifiedby="identified by '${set_MYSQL_PASSWORD}'"
  fi
  args=(\
"-e \"use mysql;\"" \
"-e \"create user if not exists '${MYSQL_USER}'@'${mysql_host}' ${identifiedby};\"" \
"-e \"alter user '${MYSQL_USER}'@'${mysql_host}' ${identifiedby};\"" \
"-e \"grant all PRIVILEGES on ${MYSQL_DATABASE}.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_DATABASE_NAME}.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_DATABASE_NAME}2.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_DATABASE_NAME}3.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
# enable failed-login tracking, such that three consecutive incorrect passwords cause temporary account locking for two days: \
# "-e \"FAILED_LOGIN_ATTEMPTS 3 PASSWORD_LOCK_TIME 2;\"" \
"-e \"select plugin from user where user='${MYSQL_USER}';\"" \
"")
  password=""
  if [ ! -z ${MYSQL_ROOT_PASSWORD:-''} ]; then
    password="--password=${MYSQL_ROOT_PASSWORD}"
  fi
  shell_prompt "exec ${sql_connect} ${sql_connect_test_host} -u ${DATABASE_USER} ${password} \
  ${args[*]} >> $LOG 2>&1" "Import test identities" "$prompt" \
  && export MYSQL_PASSWORD=${set_MYSQL_PASSWORD}
  check_log $LOG
fi
if [[ $test_checked -eq 1 ]]; then
  echo -e "
  Set of default environment
  ==========================
    Find exports for local development phase (testing) only in './Scripts/fooargs.sh')
    ";
  echo -e "
  Documented VARIABLES in config.yml
    DB=['Mysql']

  Required VARIABLES  in config.yml or Pod environment
    DATABASE_USER: <root-user>
    MYSQL_ROOT_PASSWORD: <user-password>
    MYSQL_DATABASE: <db_name>
    MYSQL_USER: <database-rw-user>
    MYSQL_PASSWORD: <user-password>
    MYSQL_HOST: <mysql-host>
    MYSQL_TCP_PORT: <mysql-tcp-port>
    TEST_DATABASE_NAME: <test_db_name>
  ==========================
  ";
  : ${MYSQL_USER?} ${MYSQL_PASSWORD?} ${MYSQL_HOST?} ${DB?}
  slogger -st $0 "Database Unit Tests... DB=${DB} TEST_DATABASE_NAME=${TEST_DATABASE_NAME}"
check_log $LOG
  cat <<EOF | tee app/Config/database.php
<?php
/** This is a source file generated by $0 . Modify it from there. */
class DATABASE_CONFIG {
private \$identities = array(
  'Mysql' => array(
    'datasource' => 'Database/MysqlCms',
    'host' => '${MYSQL_HOST}',
    'login' => '${MYSQL_USER}',
    'password' => '${MYSQL_PASSWORD}'
  )
);
public \$default = array(
  'persistent' => false,
  'host' => '',
  'login' => '',
  'password' => '',
  'database' => '${MYSQL_DATABASE}',
  'prefix' => ''
);
public \$test = array(
  'persistent' => false,
  'host' => '',
  'login' => '',
  'password' => '',
  'database' => '${TEST_DATABASE_NAME}',
  'prefix' => ''
);
public \$test2 = array(
  'persistent' => false,
  'host' => '',
  'login' => '',
  'password' => '',
  'database' => '${TEST_DATABASE_NAME}2',
  'prefix' => ''
);
public \$test_database_three = array(
  'persistent' => false,
  'host' => '',
  'login' => '',
  'password' => '',
  'database' => '${TEST_DATABASE_NAME}3',
  'prefix' => ''
);
public function __construct() {
  \$db = 'Mysql';
  if (!empty(\$_SERVER['DB'])) {
    \$db = \$_SERVER['DB'];
  }
  foreach (array('default', 'test', 'test2', 'test_database_three') as \$source) {
    \$config = array_merge(\$this->{\$source}, \$this->identities[\$db]);
    \$this->{\$source} = \$config;
  }
}
}
EOF
    slogger -st $0 "${green}Unit Test was set up in app/Config/database.php ${nc}"
fi
if [[ $update_checked -eq 1 ]]; then
  bash -c "./Scripts/start_daemon.sh ${docker} update ${ck_args}"
fi
if [[ $test_checked -eq 1 ]]; then
  bash -c "./Scripts/bootstrap.sh ${docker} test app AllTests --stderr"
fi
