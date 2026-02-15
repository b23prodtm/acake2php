#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
# shellcheck source=Scripts/lib/util.sh
. "$TOPDIR/Scripts/lib/util.sh"
parse_args "$@" <<EOF
flag runner -r --runner
end
EOF
parse_args "$@" <<EOF
flag docker -d --docker
end
EOF
pargs=$(parse_arg_trim "-[rR]+|--runner|-[dD]+|--docker"  "$@")
log_debug ": $0 [$runner] [$docker] [$pargs]"
composer_args="-d $TOPDIR update --no-interaction"
composer_nodev="--no-dev"
composer="${composer_args} ${composer_nodev}"
if [ -n "$docker" ]; then
  # shellcheck source=Scripts/fooargs.sh
  . "$TOPDIR/Scripts/fooargs.sh" "$@"
else
  # shellcheck source=Scripts/bootargs.sh
  . "$TOPDIR/Scripts/bootargs.sh" "$@"
fi
usage=("" \
"Usage: $0 [-r|-d] [-p password -s hash [-f filename]]" \
"          [[-m|--mig-database] [options]]" \
"          -r,--runner       Production Mode with container runner" \
"          -d,--docker       Test with Docker Machine" \
"          -p,--password <password> -s <hash> [-f <save-filename>]" \
"                         Setup administrator <password> with md5 <hash>. " \
"                         (Optional) A filename to save a shell script export." \
"          -m, --mig-database [options]" \
"                         Migrate Database (see $0 --mig-database --help)" \
"          --development  Install composer dependencies" \
"")
saved=( "$@" )
#; if the full set of the arguments exists, there won't be any prompt in the shell
while [[ "$#" -gt 0 ]]; do case $1 in
  -[pP]*|--password)
    shell_prompt "$TOPDIR/Scripts/config_etc_pass.sh -p ${*:2}" "${cyan}Step 1. Get an encrypted password.\n${nc}" "-Y"
    show_password_status "admin" "MASTER_PASSWORD_HASH" "was set up."
    shift;;
  -[mM]*|--mig-database)
    if [ -n "$docker" ]; then
       docker="--docker"
    fi
    if [ -n "$runner" ]; then
       runner="--runner"
    fi
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
  -[rR]*|--runner )
    # shellcheck disable=SC2154
    log_debug "${green}--runner mode...${nc}"
    ;;
  -[dD]*|--docker )
    log_daemon_msg "check database container id"
    docker ps -q -a -f "name=$(docker_name "$SECONDARY_HUB")"
    ;;
  --dev* )
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
log_daemon_msg "Cake patches"
#; patches
patches "Config/core.php"
patches "vendor/cakephp/cakephp/src/Console/ShellDispatcher.php"
patches "vendor/cakephp/cakephp/src/Console/ConsoleOutput.php" 
