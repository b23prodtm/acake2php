#!/usr/bin/env bash
set -eu

# Fixes env variables unset
DOCKER_USER="${DOCKER_USER:-betothreeprod}" COLUMNS=0 LINES=0 SYSTEMD_NO_WRAP=0

TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
APPPATH="app"
SRCPATH="app/vendor/cakephp/cakephp/src"
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
runner=$(parse_arg "-[rR]+|--runner"  "$@")
docker=$(parse_arg "--docker" "$@")
pargs=$(parse_arg_trim "--docker|-[rR]+|--runner" "$@")
composer_args="-d $APPPATH update --no-interaction --no-dev"
if [ -n "$runner" ]; then
  slogger -st "$0" "Bootargs...: ${pargs}"
  # shellcheck source=Scripts/bootargs.sh
  . "$TOPDIR/Scripts/bootargs.sh" "$@"
else
  slogger -st "$0" "Locally Testing values, bootargs...: ${pargs}"
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
    #; GET_HASH_PASSWORD
    shell_prompt "$TOPDIR/Scripts/config_etc_pass.sh -p ${*:2}" "${cyan}Step 1. Get an encrypted password.\n${nc}" "-Y"
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
    echo -e "${green}--runner mode: Fixing some file permissions...${nc}"
    # shellcheck source=Scripts/configure_tmp.sh
    bash -c "$TOPDIR/Scripts/configure_tmp.sh"
    ;;
  --docker )
    slogger -st docker "check database container id"
    docker ps -q -a -f "name=$(docker_name "$SECONDARY_HUB")"
    ;;
  --development )
    composer_args="-d $APPPATH update --no-interaction --dev"
    ;;
  -[vV]*|--verbose )
    set -x
    echo "Passed params : ${BASH_SOURCE[*]} ${saved[*]}";;
    *) echo "Unknown parameter: ${BASH_SOURCE[0]} $1"; exit 1;;
esac; shift; done
#; update plugins and dependencies
bash -c "$TOPDIR/Scripts/composer.sh ${composer_args}"
slogger -st sed "Cake patches $APPPATH and $SRCPATH"
#; patches
patches "$APPPATH/app/bin/cake.php"
patches "$APPPATH/Config/core.php"
patches "$SRCPATH/Console/ShellDispatcher.php" "$SRCPATH/Console/ConsoleOutput.php" 
