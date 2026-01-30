#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
# shellcheck source=Scripts/lib/util.sh
. "$TOPDIR/Scripts/lib/util.sh"

parse_args_lazy "$@" <<EOF
flag verbose -v --verbose
flag runner -r --runner
flag docker -d --docker
option password -p --password
option salt -s --salt
option file -f --file
flag migrate -m --mig-data
flag dev -x --dev
flag help -h --help
option group -g --group
end
EOF

composer_args=( "-d" "$TOPDIR" "update" "--no-interaction" )
composer_nodev=( "--no-dev" )
usage() {
  printf "%s\n" \
  "Usage: ${BASH_SOURCE[0]} [-r|-d] [-p password -s hash [-f filename]]" \
  "          [[-m|--mig-database] [options]]" \
  "          -r,--runner    Production Mode with container runner" \
  "          -d,--docker    Test with Docker Machine" \
  "          -p,--password <password> -s <hash> [-f <save-filename>]" \
  "                         Setup administrator <password> with md5 <hash>. " \
  "                         (Optional) A filename to save secret to." \
  "          -m, --mig-data [options]" \
  "                         Migrate Database (see $0 --mig-data --help)" \
  "          -v,--verbose   steps progress" \
  "          -x,--dev       Install composer dependencies" \
  "          -g, --group    Temp Paths and Files permissions: Group name (HTTPD Log)" \
  "          -h, --help     Display Help" \
  ""
}
saved=( "$@" )
if [ "$help" -gt 0 ]; then 
  usage
  exit 0
fi
if [ ${#group} -gt 0 ]; then
  group_args=( "-g" "$group" )
fi
if [ "$dev" -gt 0 ]; then
  composer_args=( "${composer_args[@]}" "-W" )
else
  composer_args=( "${composer_args[@]}" "${composer_nodev[@]}" )
fi
if [ "$verbose" -gt 0 ]; then
    set -x
    log_progress_msg "Passed params : ${BASH_SOURCE[*]} ${saved[*]}"
fi
log_progress_msg "If the full set of the arguments exists, there won't be any prompt in the shell"
if [ ${#password} -gt 0 ]; then
  log_progress_msg "MASTER_PASSWORD"
  shell_prompt "$TOPDIR/Scripts/config_etc_pass.sh" \
 "Step 1. Get an encrypted password.\n" "-Y" \
 -p "$password" -s "$salt" -f "$file"
  show_password_status "admin" "MASTER_PASSWORD_HASH" "was set up."
fi
[ "$runner" -gt 0 ] && set -- "--runner" "$@"
if [ "$docker" -gt 0 ]; then
  set -- "--docker" "$@"
  log_progress_msg "Check database container id"
  docker ps -q -a -f "name=$(docker_name "$SECONDARY_HUB")"
  # shellcheck source=Scripts/fooargs.sh
  . "$TOPDIR/Scripts/fooargs.sh" "$@"
else
  # shellcheck source=Scripts/bootargs.sh
  . "$TOPDIR/Scripts/bootargs.sh" "$@"
fi
if [ "$migrate" -gt 0 ]; then
  log_progress_msg "MIGRATION"
  shell_prompt "$TOPDIR/migrate-database.sh" "Step 2. Migrate database\n" "-Y" "$@"
fi
log_progress_msg "Setup paths and file permissions"
. "$TOPDIR/Scripts/configure_path.sh" "${group_args[@]}"
log_progress_msg "Filter templates"
. "$TOPDIR/Scripts/cp_bkp_old.sh" "Config/" "app_local.template" "app_local.php"
log_progress_msg "Download plugins and dependencies"
. "$TOPDIR/Scripts/composer.sh" "${composer_args[@]}"
log_progress_msg "Cake patches"
patches "Config/core.php"
patches "vendor/cakephp/cakephp/src/Console/ShellDispatcher.php"
patches "vendor/cakephp/cakephp/src/Console/ConsoleOutput.php" 
