#!/usr/bin/env bash
set -euo
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
runner=$(parse_arg "-[cC]+|--runner" "$@")
docker=$(parse_arg "--docker" "$@")
travis=$(parse_arg "--travis" "$@")
pargs=$(parse_arg_trim "-[cC]+|--runner|--docker|--travis" "$@")
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
dbfile=Config/app_local.template
schemafile=Config/Schema/AppSchema.template
usage=("" \
"Usage: $0 [-r] [-i] [-u]" \
"          -------------" \
"          -r, --runner, --travis" \
"                      CircleCI and self-host runner or Travis CI job" \
"          -i          Make the migration files from ${dbfile} and ${schemafile}" \
"          -u          Migrate the database in Config/Migrations/" \
"          -v, --verbose" \
"                      Outputs more debug information" \
"          -h, --help  Displays this help" \
"")
# shellcheck disable=SC2153
mode=0x00000
test_bit=0x10000
runner_bit=0x01000
update_bit=0x00100
docker_bit=0x00010
initialize_bit=0x00001
saved=( "$@" )
cx_args="--connection=default"
# test_args="app AllTests --stderr"
test_args="app Controller/PagesController --stderr >> $LOG"
MARIADB_SHORT_NAME=$(docker_name "$SECONDARY_HUB")
while [ "$#" -gt 0 ]; do case "$1" in
  --docker )
    mode=$((mode | docker_bit))
    bash -c "$TOPDIR/Scripts/start_daemon.sh ${docker}"
    ;;
  -[uU]* )
    mode=$((mode | update_bit))
    ;;
  --connection=test )
    cx_args="$1"
    mode=$((mode | test_bit))
    ;;
  --connection* )
    cx_args="$1"
    ;;
  -[iI]* )
    mode=$((mode | initialize_bit))
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
  -[rR]*|--runner|--travis)
    mode=$((mode | runner_bit))
    ;;
  --testunitbase*)
    # Transform long options to short ones
    arg=$1; shift
    # shellcheck disable=SC2046
    set -- $(echo "${arg}" \
    | awk 'BEGIN{ FS="[ =]+" }{ print "-u " $2 }') "$@"
    mode=$((mode | test_bit))
    cx_args="--connection=test"
    shift $((OPTIND -1))
    ;;
  *) echo "Invalid parameter: ${BASH_SOURCE[0]} $1" && exit 1;;
  esac
shift; #echo "$@";
done
# configure user application database and eventually alter user database access
# shellcheck disable=SC2154
initialize() {
	[ "$#" -lt 1 ] && echo "Usage: ${FUNCNAME[0]} [--docker] [<file.template or php]..." && exit 1
	slogger -st "${FUNCNAME[0]}" "$* ..."
	docker=$(parse_arg "--docker" "$@")
	homebrew=0x01
	port=0x10
	pm=0x00
	[ -z "$(command -v brew)" ] && pm=$((pm | 0x01))
	[ -z "$(command -v port)" ] && pm=$((pm | 0x10))
	sockdir=/var/run/mysqld
	while [[ "$#" -gt 0 ]]; do case $1 in
        	*.php|*.template)
                	template=$1
	                file=$(echo "$template" | cut -d . -f 1)
        	        # shellcheck source=cp_bkp_old.sh
	                . "${TOPDIR}/Scripts/cp_bkp_old.sh" "$TOPDIR" "$template" "${file}.php";;
		*);;
	esac; shift; done
	rm -Rf "$TOPDIR/Config/Migrations/*"
}
#; export -f initialize

if [[ $((mode & initialize_bit)) -gt 0 ]]; then
	# INITIALIZATION STUFF
	initialize "${dbfile}" "${schemafile}" "${docker}"
	bash -c "$TOPDIR/Scripts/configure_database.sh $(echo "$schemafile" | cut -d . -f 1).php"
fi
if [[ $((mode & (test_bit | update_bit | runner_bit | docker_bit))) -gt 0 ]]; then
  pargs="$travis $docker $runner"
  if [[ $((mode & test_bit)) -gt 0 ]]; then
      pargs="$pargs test $test_args"
  elif [[ $((mode & update_bit)) -gt 0 ]]; then
      pargs="$pargs update $cx_args"
  fi
  bash -c "$TOPDIR/Scripts/bootstrap.sh $pargs"
  check_log "$LOG"
fi
