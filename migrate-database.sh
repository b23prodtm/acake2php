#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/parsing.sh
. "$TOPDIR/Scripts/lib/parsing.sh"
# shellcheck source=Scripts/lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
runner=$(parse_arg "-[rR]+|--runner" "$@")
docker=$(parse_arg "--docker" "$@")
travis=$(parse_arg "--travis" "$@")
pargs=$(parse_arg_trim "-[rR]+|--runner|--docker|--travis" "$@")
if [ -n "$runner" ]; then
  slogger -st "$0" "Bootargs...: ${pargs}"
  # shellcheck source=Scripts/bootargs.sh
  . "$TOPDIR/Scripts/bootargs.sh" "$@"
else
  slogger -st "$0" "Locally Testing values, bootargs...: ${pargs}"
  # shellcheck source=Scripts/fooargs.sh
  . "$TOPDIR/Scripts/fooargs.sh" "$@"
fi
LOG=$(new_cake_log "$travis" "$runner" "$docker") && slogger -st "$0" "$LOG"
usage=("" \
"Usage: $0 [sockfile.sock] [-u] [-y|n] [-o] [-p <word>] [-t <word>] [-i] [--sql-password=<password>] [--test-sql-password=<password>]" \
"          To initialize the databases, enter in the ${MYSQL_HOST} host terminal: $0 -u -i" \
"          -------------" \
"          file.sock   Set the socket file to connect SQL database" \
"          -u          Update the database in app/config/Schema/" \
"          -y          Overwrite app.php and default socket file" \
"          -i --sql-password=<word> --test-sql-password=<word>" \
"                      Initialize databases with new passwords and reset MYSQL_DATABASE and TEST_MYSQL_DATABASE privileges" \
"          -n, --runner" \
"                      CircleCI and self-host runner: resets app.php, keep socket and update the database" \
"                      Doesn't use the socket file" \
"          --travis" \
"                      Travis CI job" \
"          -p=<password>" \
"                      Exports MYSQL_ROOT_PASSWORD" \
"          -t=<password>" \
"                      Exports MYSQL_PASSWORD" \
"          --database=<name>" \
"                      Exports MYSQL_DATABASE" \
"          --testunitbase=<name>" \
"                      Exports TEST_MYSQL_DATABASE" \
"          --enable-ed25519-plugin" \
"                      Enable MariaDB plugin https://mariadb.com/kb/en/authentication-plugin-ed25519/" \
"          -v, --verbose" \
"                      Outputs more debug information" \
"          -h, --help  Displays this help" \
"")
sql_connect="mysql"
# shellcheck disable=SC2153
sql_connect_host="-h ${MYSQL_HOST} -P ${MYSQL_TCP_PORT}"
dbfile=app/config/app_local.template
schemafile=app/config/Schema/schema.template
sockfile=/tmp/mysqld.sock
config_app_checked="-Y"
mode=0x00000
test_bit=0x10000
runner_bit=0x01000
update_bit=0x00100
docker_bit=0x00010
initialize_bit=0x00001
saved=( "$@" )
authentication_plugin=0
mysql_host="%"
cx_args="--connection=default"
# test_args="app AllTests --stderr"
test_args="app Controller/PagesController --stderr >> $LOG"
MARIADB_SHORT_NAME=$(docker_name "$SECONDARY_HUB")
while [ "$#" -gt 0 ]; do case "$1" in
  --enable-ed25519-plugin*)
    slogger -st "$0" "Enabled auth_ed25519 plugin for passwords..."
    log_warning_msg "Plugin Not available from PHP PDO connect (you should avoid using it)"
    authentication_plugin="ed25519";;
  --docker )
    mode=$((mode | docker_bit))
    bash -c "./Scripts/start_daemon.sh ${docker}"
    # Running docker ... mysql's allowed to connect without any local mysql installation
    docker exec "$MARIADB_SHORT_NAME" hostname 2>> "$LOG"
    sql_connect="docker exec $MARIADB_SHORT_NAME mysql"
    sockfile="$(pwd)/deployment/images/mysqldb/mysqld/mysqld.sock"
    ;;
  -[uU]* )
    mode=$((mode | update_bit))
    ;;
  --connection=test )
    cx_args="$1"
    mode=$((mode | test_bit))
    ;;
  --connection* )
    cx_args="$1";;
  *.sock ) sockfile=$1;;
  -[iI]* )
    mode=$((mode | initialize_bit))
    ;;
  --sql-password*)
    OPTIND=1
    parse_sql_password "set_MYSQL_PASSWORD" "Altering ${MYSQL_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  --test-sql-password*)
    mode=$((mode | test_bit))
    cx_args="--connection=test"
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
    cx_args="${cx_args} -v"
    test_args="${test_args} -v"
    ;;
  -[hH]*|--help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[nN]*|--runner|--travis)
    mode=$((mode | runner))
    sockfile=""
    config_app_checked="-N"
    ;;
  -[pP]* )
    parse_sql_password "MYSQL_ROOT_PASSWORD" "current ${MYSQL_ROOT_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  -[tT]* )
    mode=$((mode | test_bit))
    cx_args="--connection=test"
    parse_sql_password "MYSQL_PASSWORD" "current ${MYSQL_USER} password" "$@"
    shift $((OPTIND -1))
    ;;
  --database*)
    # Transform long options to short ones
    arg=$1; shift
    # shellcheck disable=SC2046
    set -- $(echo "${arg}" \
    | awk 'BEGIN{ FS="[ =]+" }{ print "-d " $2 }') "$@"
    parse_and_export "d" "MYSQL_DATABASE" "${MYSQL_ROOT_USER} database name" "$@"
    shift $((OPTIND -1))
    ;;
  --testunitbase*)
    # Transform long options to short ones
    arg=$1; shift
    # shellcheck disable=SC2046
    set -- $(echo "${arg}" \
    | awk 'BEGIN{ FS="[ =]+" }{ print "-u " $2 }') "$@"
    mode=$((mode | test_bit))
    cx_args="--connection=test"
    parse_and_export "u" "TEST_MYSQL_DATABASE" "${MYSQL_USER} database name" "$@"
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
if [[ $((mode & initialize_bit)) -gt 0 ]]; then
  #; ---------------------------------- set MYSQL_PASSWORD
  slogger -st "$0" "\r${red}WARNING: You will modify SQL ${MYSQL_USER} password !${nc}"
  export set_MYSQL_PASSWORD=${set_MYSQL_PASSWORD:-$MYSQL_PASSWORD}
  prompt="-Y"
  if [ -z "${set_MYSQL_PASSWORD}" ]; then
    slogger -st "$0" "\r${orange}WARNING: Using blank password for ${MYSQL_USER} !!${nc}"
    prompt=${DEBIAN_FRONTEND:-''}
  fi
  if [ $authentication_plugin = "ed25519" ]; then
    identifiedby="IDENTIFIED VIA ed25519 USING '${set_MYSQL_PASSWORD}'"
  else
    identifiedby="identified by '${set_MYSQL_PASSWORD}'"
  fi
  # ALTER USER is MariaDB 10.2 and above waiting for ARM binary
  # "-e \"alter user '${MYSQL_USER}'@'${mysql_host}' ${identifiedby};\"" \
  args=(\
"-e \"use mysql;\"" \
"-e \"create user if not exists '${MYSQL_USER}'@'${mysql_host}' ${identifiedby};\"" \
"-e \"SET PASSWORD FOR '${MYSQL_USER}'@'${mysql_host}'=PASSWORD('${set_MYSQL_PASSWORD}');\"" \
"-e \"grant all PRIVILEGES on ${MYSQL_DATABASE}.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_MYSQL_DATABASE}.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_MYSQL_DATABASE}_2.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"grant all PRIVILEGES on ${TEST_MYSQL_DATABASE}_3.* to '${MYSQL_USER}'@'${mysql_host}';\"" \
"-e \"flush PRIVILEGES;\"" \
"-e \"select plugin from user where user='${MYSQL_USER}';\"")
  # enable failed-login tracking, such that three consecutive incorrect passwords cause temporary account locking for two days:
  # "-e \"FAILED_LOGIN_ATTEMPTS 3 PASSWORD_LOCK_TIME 2;\""
  shell_prompt "${sql_connect} ${sql_connect_host} -uroot ${MYSQL_ROOT_PASSWORD} \
  ${args[*]} >> $LOG 2>&1" "Import test identities" "$prompt" \
  && export MYSQL_PASSWORD=${set_MYSQL_PASSWORD}
  check_log "$LOG"
fi
if [[ $((mode & (test_bit | update_bit | runner_bit | docker_bit))) -gt 0 ]]; then
  pargs=" $travis $docker $runner"
  if [[ $((mode & test_bit)) -gt 0 ]]; then
      pargs="$pargs test $test_args"
  elif [[ $((mode & update_bit)) -gt 0 ]]; then
      pargs="$pargs update $cx_args"
  fi
  bash -c "./Scripts/bootstrap.sh $pargs"
  check_log "$LOG"
fi
