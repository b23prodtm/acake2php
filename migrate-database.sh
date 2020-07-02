#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/test/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/test/parsing.sh
. "$TOPDIR/Scripts/lib/parsing.sh"
# shellcheck source=Scripts/lib/test/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
openshift=$(parse_arg "-[oO]+|--openshift" "$@")
docker=$(parse_arg "--docker" "$@")
travis=$(parse_arg "--travis" "$@")
pargs=$(parse_arg_trim "-[oO]+|--openshift|--docker|--travis" "$@")
if [ -n "$openshift" ]; then
  slogger -st "$0" "Bootargs...: ${pargs}"
  # shellcheck source=Scripts/bootargs.sh
  . "$TOPDIR/Scripts/bootargs.sh" "$@"
else
  slogger -st "$0" "Locally Testing values, bootargs...: ${pargs}"
  # shellcheck source=Scripts/fooargs.sh
  . "$TOPDIR/Scripts/fooargs.sh" "$@"
fi
LOG=$(new_cake_log "$travis" "$openshift" "$docker") && slogger -st "$0" "$LOG"
usage=("" \
"Usage: $0 [-u] [-y|n] [-o] [-p <word>] [-t <word>] [-i] [--sql-password=<password>] [--test-sql-password=<password>]" \
"          -u          Update the database in app/Config/Schema/" \
"          -y          Reset database.php and default socket file" \
"          -n          Doesn't reset database.php and socket" \
"          -i --sql-password=<word> --test-sql-password=<word>" \
"                      Import SQL identities with new passwords and reset MYSQL_DATABASE and TEST_DATABASE_NAME privileges" \
"          -o, --openshift, --travis" \
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
# shellcheck disable=SC2153
sql_connect_host="-h ${MYSQL_HOST} -P ${MYSQL_TCP_PORT}"
dbfile=database.cms.php
schemafile=Schema/schema.cms.php
sockfile=/tmp/mysqld.sock
config_app_checked="-Y"
test_checked=0
update_checked=0
import_identities=0
saved=("$@")
authentication_plugin=0
mysql_host="%"
ck_args="--connection=default"
# test_args="app AllTests --stderr"
test_args="app Controller/PagesController --stderr >> $LOG"
MARIADB_SHORT_NAME=$(docker_name "$SECONDARY_HUB")
while [ "$#" -gt 0 ]; do case "$1" in
  --enable-authentication-plugin*)
    slogger -st "$0" "Enabled auth_ed25519 plugin..."
    authentication_plugin=1;;
  --docker )
    bash -c "./Scripts/start_daemon.sh ${docker}"
    # Running docker ... mysql's allowed to connect without any local mysql installation
    docker exec "$MARIADB_SHORT_NAME" hostname 2>> "$LOG"
    sql_connect="docker exec $MARIADB_SHORT_NAME mysql"
    sockfile="$(pwd)/mysqldb/mysqld/mysqld.sock"
    ;;
  -[uU]* )
    update_checked=1
    ;;
  --connection=test )
    ck_args="$1"
    test_checked=1
    ;;
  --connection* )
    ck_args="$1";;
  *.sock ) sockfile=$1;;
  -[nN]* )
    sockfile=""
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
    test_checked=1
    ck_args="--connection=test"
    OPTIND=1
    parse_sql_password "set_MYSQL_PASSWORD" "Altering ${MYSQL_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[vV]*|--verbose )
    # Reset passed args (shift reset)
    text=("" \
"Passed params : $0 ${saved[*]}" \
"and environment VARIABLES:" \
"$(export -p | grep "DATABASE\|MYSQL")" \
"")
    printf "%s\n" "${text[@]}"
    ck_args="${ck_args} -v"
    test_args="${test_args} -v"
    ;;
  -[hH]*|--help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[oO]*|--openshift);;
  --travis)
    ;;
  -[pP]* )
    parse_sql_password "MYSQL_ROOT_PASSWORD" "current ${DATABASE_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[tT]* )
    test_checked=1
    ck_args="--connection=test"
    printf "Testing %s Unit..." $test_checked
    parse_sql_password "MYSQL_PASSWORD" "current ${MYSQL_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  --database*)
    # Transform long options to short ones
    arg=$1; shift
    # shellcheck disable=SC2046
    set -- $(echo "${arg}" \
    | awk 'BEGIN{ FS="[ =]+" }{ print "-d " $2 }') "$@"
    parse_and_export "d" "MYSQL_DATABASE" "${DATABASE_USER} database name" "$@"
    shift $((OPTIND -1))
    ;;
  --testunitbase*)
    # Transform long options to short ones
    arg=$1; shift
    # shellcheck disable=SC2046
    set -- $(echo "${arg}" \
    | awk 'BEGIN{ FS="[ =]+" }{ print "-u " $2 }') "$@"
    test_checked=1
    ck_args="--connection=test"
    parse_and_export "u" "TEST_DATABASE_NAME" "${MYSQL_USER} database name" "$@"
    shift $((OPTIND -1))
    ;;
  *) echo "Invalid parameter: ${BASH_SOURCE[0]} $1" && exit 1;;
  esac
shift; #echo "$@";
done
# configure user application database and eventually alter user database access
# shellcheck disable=SC2154
shell_prompt "$TOPDIR/Scripts/config_app_database.sh ${dbfile} ${schemafile} ${sockfile} ${docker}" \
"${cyan}Setup ${dbfile} connection and socket\n${nc}" "$config_app_checked"
if [[ $import_identities -eq 1 ]]; then
  #; ---------------------------------- set MYSQL_ROOT_PASSWORD
  export set_DATABASE_PASSWORD=${set_DATABASE_PASSWORD:-$MYSQL_ROOT_PASSWORD}
  # shellcheck disable=SC2154
  log_warning_msg "${red}WARNING: You will modify SQL ${DATABASE_USER} password !${nc}"
  prompt="-Y"
  if [ -z "${set_DATABASE_PASSWORD}" ]; then
    # shellcheck disable=SC2154
     log_warning_msg "${orange}WARNING: Using blank password for ${DATABASE_USER} !!${nc}"
    prompt=${DEBIAN_FRONTEND:-''}
  fi
  if [ $authentication_plugin = 1 ]; then
    identifiedby="IDENTIFIED VIA ed25519 USING '${set_DATABASE_PASSWORD}'"
  else
    identifiedby="identified by '${set_DATABASE_PASSWORD}'"
  fi
  args=(\
"-e \"select version();\"" \
"-e \"use mysql;\"" \
"-e \"create user if not exists '${DATABASE_USER}'@'${mysql_host}' ${identifiedby};\"" \
# ALTER USER is MariaDB 10.2 and above waiting for ARM binary
# "-e \"alter user '${DATABASE_USER}'@'${mysql_host}' ${identifiedby};\"" \
"-e \"SET PASSWORD FOR '${DATABASE_USER}'@'${mysql_host}' = PASSWORD('${set_DATABASE_PASSWORD}');\"" \
"-e \"grant all PRIVILEGES on *.* to '${DATABASE_USER}'@'${mysql_host}' WITH GRANT OPTION;\"" \
"-e \"flush PRIVILEGES;\"" \
"-e \"create database if not exists ${MYSQL_DATABASE} default character set='utf8' default collate='utf8_bin';\"" \
"-e \"create database if not exists ${TEST_DATABASE_NAME};\"" \
"-e \"create database if not exists ${TEST_DATABASE_NAME}_2;\"" \
"-e \"create database if not exists ${TEST_DATABASE_NAME}_3;\"" \
# enable failed-login tracking, such that three consecutive incorrect passwords cause temporary account locking for two days: \
# "-e \"FAILED_LOGIN_ATTEMPTS 3 PASSWORD_LOCK_TIME 2;\"" \
"-e \"select plugin from user where user='${DATABASE_USER}';\"" \
"-e \"show databases;\"" \
"")
  slogger -st "$0" "Forked script to keep hidden table user secrets..."
  password=""
  if [ -n "${MYSQL_ROOT_PASSWORD:-}" ]; then
    password="--password=${MYSQL_ROOT_PASSWORD}"
  fi
  shell_prompt "${sql_connect} ${sql_connect_host} -u ${DATABASE_USER} ${password} \
  ${args[*]} >> $LOG 2>&1" "Import default identities" "$prompt"\
  && export MYSQL_ROOT_PASSWORD=${set_DATABASE_PASSWORD}
  #; ---------------------------------- set MYSQL_PASSWORD
  slogger -st "$0" "\r${red}WARNING: You will modify SQL ${MYSQL_USER} password !${nc}"
  export set_MYSQL_PASSWORD=${set_MYSQL_PASSWORD:-$MYSQL_PASSWORD}
  if [ -z "${set_MYSQL_PASSWORD}" ]; then
    slogger -st "$0" "\r${orange}WARNING: Using blank password for ${MYSQL_USER} !!${nc}"
    prompt=${DEBIAN_FRONTEND:-''}
  fi
  if [ $authentication_plugin = 1 ]; then
    identifiedby="IDENTIFIED VIA ed25519 USING '${set_MYSQL_PASSWORD}'"
  else
    identifiedby="identified by '${set_MYSQL_PASSWORD}'"
  fi
  args=(\
"-e \"use mysql;\"" \
"-e \"create user if not exists '${MYSQL_USER}'@'${mysql_host}' ${identifiedby};\"" \
# ALTER USER is MariaDB 10.2 and above waiting for ARM binary
# "-e \"alter user '${MYSQL_USER}'@'${mysql_host}' ${identifiedby};\"" \
"-e \"SET PASSWORD FOR '${MYSQL_USER}'@'${mysql_host}' = PASSWORD('${set_MYSQL_PASSWORD}');\"" \
"-e \"grant all PRIVILEGES on ${MYSQL_DATABASE}.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_DATABASE_NAME}.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_DATABASE_NAME}_2.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_DATABASE_NAME}_3.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
# enable failed-login tracking, such that three consecutive incorrect passwords cause temporary account locking for two days: \
# "-e \"FAILED_LOGIN_ATTEMPTS 3 PASSWORD_LOCK_TIME 2;\"" \
"-e \"select plugin from user where user='${MYSQL_USER}';\"" \
"-e \"flush PRIVILEGES;\"")
  password=""
  if [ -n "${MYSQL_ROOT_PASSWORD:-}" ]; then
    password="--password=${MYSQL_ROOT_PASSWORD}"
  fi
  shell_prompt "${sql_connect} ${sql_connect_host} -u ${DATABASE_USER} ${password} \
  ${args[*]} >> $LOG 2>&1" "Import test identities" "$prompt" \
  && export MYSQL_PASSWORD=${set_MYSQL_PASSWORD}
  check_log "$LOG"
fi
if [[ $update_checked -eq 1 ]]; then
  bash -c "./Scripts/start_daemon.sh ${travis} ${docker} update ${ck_args}"
fi
if [[ $test_checked -eq 1 ]]; then
  echo "GOAL $travis $docker $test_args"
  bash -c "./Scripts/bootstrap.sh ${travis} ${docker} test ${test_args}"
  check_log "$LOG"
fi
