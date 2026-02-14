#!/usr/bin/env bash
set -eu

# Fixes env variables unset
DOCKER_USER="${DOCKER_USER:-betothreeprod}" COLUMNS=0 LINES=0 SYSTEMD_NO_WRAP=0

TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
# shellcheck source=Scripts/lib/util.sh
. "$TOPDIR/Scripts/lib/util.sh"
runner=$(parse_arg "-[rR]+|--runner"  "$@")
docker=$(parse_arg "--docker" "$@")
pargs=$(parse_arg_trim "--docker|-[rR]+|--runner" "$@")
composer_args="-d $TOPDIR update --no-interaction"
composer_nodev="--no-dev"
composer="${composer_args} ${composer_nodev}"
if [ -n "$runner" ]; then
  log_daemon_msg "Bootargs...: ${pargs}"
  # shellcheck source=Scripts/bootargs.sh
  . "$TOPDIR/Scripts/bootargs.sh" "$@"
else
  log_daemon_msg "Locally Testing values, bootargs...: ${pargs}"
  # shellcheck source=Scripts/fooargs.sh
  . "$TOPDIR/Scripts/fooargs.sh" "$@"
fi
usage=("" \
"Usage: $0 [-r|--runner|--travis] [-p password -s hash [-f filename]]" \
"          [[-d|--mig-database] [options]]" \
"          --runner       A test or migrate for CI self-host runner build" \
"          -p,--password <password> -s <hash> [-f <save-filename>]" \
"                         Setup administrator <password> with md5 <hash>. " \
"                         (Optional) A filename to save a shell script export." \
"          -d, --mig-database [options]" \
"                         Migrate Database (see $0 --mig-database --help)" \
"          --development  Install composer dependencies" \
"")
saved=( "$@" )
show_password_status "root" "MYSQL_ROOT_PASSWORD" "is configuring ${runner} ${docker}..."
#; if the full set of the arguments exists, there won't be any prompt in the shell
while [[ "$#" -gt 0 ]]; do case $1 in
  -[pP]*|--password)
    shell_prompt "$TOPDIR/Scripts/config_etc_pass.sh -p ${*:2}" "${cyan}Step 1. Get an encrypted password.\n${nc}" "-Y"
    show_password_status "admin" "MASTER_PASSWORD_HASH" "was set up."
    shift;;
  -[dD]*|--mig-database)
    shell_prompt "$TOPDIR/migrate-database.sh ${docker} ${runner} ${*:2}" "${cyan}Step 2. Migrate database\n${nc}" "-Y"
    break;;
  -[sS]*|-[fF]*)
    #; void --password known args
    OPTIND=1
    if [[ "$#" -gt 1 ]]; then
      arg=$2; [[ "${arg:0:1}" != '-' ]] && OPTIND=2
    fi
    shift $((OPTIND -1))
    ;;
  --help )
    printf "%s\n" "${usage[@]}"
    exit 0;;
  -[rR]*|--runner|--travis )
    # shellcheck disable=SC2154
    echo -e "${green}--runner mode...${nc}"
    ;;
  --docker )
    slogger -st docker "check database container id"
    docker ps -q -a -f "name=$(docker_name "$SECONDARY_HUB")"
    ;;
  --development )
    composer="$composer_args -W"
    ;;
  -[vV]*|--verbose )
    set -x
    echo "Passed params : ${BASH_SOURCE[*]} ${saved[*]}";;
    *) echo "Unknown parameter: ${BASH_SOURCE[0]} $1"; exit 1;;
esac; shift; done
#; Setup paths and file permissions 
bash -c "$TOPDIR/Scripts/configure_path.sh"
#; filter template
bash -c "$TOPDIR/Scripts/cp_bkp_old.sh Config/ app_local.template app_local.php"
#; download plugins and dependencies
bash -c "$TOPDIR/Scripts/composer.sh ${composer}"
slogger -st sed "Cake patches"
#; patches
patches "Config/core.php"
patches "vendor/cakephp/cakephp/src/Console/ShellDispatcher.php"
patches "vendor/cakephp/cakephp/src/Console/ConsoleOutput.php" 
