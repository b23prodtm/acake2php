#!/usr/bin/env bash
set -euo
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"

dbfile=Config/app_local.template
schemafile=Config/Schema/AppSchema.template
usage=("" \
"Usage: $0 [-r] [-i] [-u] [-d]" \
"          -------------" \
"          -r, --runner" \
"                      Environment variables are provided by remote container orchestrator (Production Mode)" \
"          -d, --docker" \
"                      Environment variables are provided by a DockerMachine (Local Test Mode)" \
"          -i          Make the migration files from ${dbfile} and ${schemafile}" \
"          -u          Migrate the database in Config/Migrations/" \
"          -v, --verbose" \
"                      Outputs more debug information" \
"          -h, --help  Displays this help" \
"")
# shellcheck disable=SC2153
saved=( "$@" )
cx_args="--connection=default"
# test_args="app AllTests --stderr"
test_args="app Controller/PagesController --stderr"
MARIADB_SHORT_NAME=$(docker_name "$SECONDARY_HUB")
parse_args "$@" <<EOF
flag runner -r --runner
flag docker -d --docker
flag update -u --update
flag initialize -i --init
flag verbose -v --verbose
flag help -h --help
option connection -c --connection
end
EOF
if [ -n "$verbose" ]; then
  text=("" \
  "Passed params : $0 ${saved[*]}" \
  "and environment VARIABLES:" \
  "$(export -p | grep "DATABASE\|MYSQL|PASSWORD")" \
  "")
  log_debug "$(printf "%s\n" "${text[@]}")"
  cx_args="${cx_args} -v"
  test_args="${test_args} -v"
fi
if [ -n "$help" ]; then
  printf "%s\n" "${usage[@]}"
  exit 0
fi
if [ "$connection" = "test" ]; then
  # Transform long options to short ones
  mode=$((mode | test_bit))
  test_args="$test_args $1"
elif [ -n "$connection" ]; then
  cx_args="--connection=$connection"
fi

# configure user application database and eventually alter user database access
# shellcheck disable=SC2154
initialize() {
	[ "$#" -lt 1 ] && echo "Usage: ${FUNCNAME[0]} [<file.template or php]..." && exit 1
	log_debug "$(log_progress_msg "${FUNCNAME[0]} $* ...")"
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

if [ -n "$initialize" ]; then
	log_progress_msg "INITIALIZATION STUFF"
	initialize "${dbfile}" "${schemafile}"
	bash -c "$TOPDIR/Scripts/configure_database.sh $(echo "$schemafile" | cut -d . -f 1).php"
fi
if [ -n "$docker" ]; then
  set -- "--docker" "$@"
  # shellcheck source=Scripts/fooargs.sh
  . "$TOPDIR/Scripts/fooargs.sh" "$@"
  bash -c "$TOPDIR/Scripts/start_daemon.sh $*"
else
  # shellcheck source=Scripts/bootargs.sh
  . "$TOPDIR/Scripts/bootargs.sh" "$@"
fi
[ -n "$runner" ] && set -- "--runner" "$@"
[ -n "$test" ] && set -- "-t" "$test_args" "$@"
[ -n "$update" ] && set -- "-u" "$cx_args" "$@"
bash -c "$TOPDIR/Scripts/bootstrap.sh $*"
