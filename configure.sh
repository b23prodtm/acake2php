#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
APPPATH="app/vendor/cakephp/cakephp/src"
# shellcheck source=Scripts/lib/test/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# shellcheck source=Scripts/lib/test/parsing.sh
. "$TOPDIR/Scripts/lib/parsing.sh"
# shellcheck source=Scripts/lib/test/shell_prompt.sh
. "$TOPDIR/Scripts/lib/shell_prompt.sh"
runner=$(parse_arg "-[rR]+|--runner"  "$@")
docker=$(parse_arg "--docker" "$@")
pargs=$(parse_arg_trim "--docker|-[rR]+|--runner" "$@")
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
"Usage: $0 [-m] [--runner] [-c] [-h [-p password -s salt [-f filename]]]" \
"          [-m] [--runner] [-c][[-d|--mig-database] [options]]" \
"          --runner       A test or migrate for CI self-host runner build" \
"          -c,--const     Reset to $TOPDIR/app/webroot/php-cms/etc/constantes-template.properties" \
"          -h,--hash      Reset administrator password hash:" \
"               -p <password> -s <salt> [-f <save-filename>]" \
"                         Set administrator <password> with md5 <salt>. Optional file to save a shell script export." \
"          -m,--submodule Update sub-modules from Git" \
"          -d, --mig-database [options]" \
"                         Migrate Database (see $0 --mig-database --help)" \
"          --development  Install composer dependencies" \
"          -a, --apache2  Make apache2 VirtualHost configuration from templates: etc/apache2/site.tpl..." \
"")
composer_args="require --no-interaction --update-no-dev"
saved=( "$@" )
show_password_status "${DATABASE_USER}" "${MYSQL_ROOT_PASSWORD}" "is configuring ${runner} ${docker}..."
#; if the full set of the arguments exists, there won't be any prompt in the shell
while [[ "$#" -gt 0 ]]; do case $1 in
  -[cC]*|--const)
    # shellcheck disable=SC2154
    shell_prompt "$TOPDIR/Scripts/config_etc_const.sh" "${cyan}Step 1. Overwrite constantes.properties\n${nc}" "-Y"
    ;;
  -[hH]*|--hash)
    #; get hash password
    shell_prompt "$TOPDIR/Scripts/config_etc_pass.sh ${*:2}" "${cyan}Step 2. Get a hashed password with encryption, PHP encrypts.\n${nc}" "-Y"
    ;;
  -[dD]*|--mig-database)
    #; [[-d|--mig-database] [-u]] argument fixes up : Error: Database connection "Mysql" is missing, or could not be created.
    shell_prompt "$TOPDIR/migrate-database.sh ${docker} ${runner} ${*:2}" "${cyan}Step 3. Migrate database\n${nc}" "-Y"
    break;;
  -[sS]*|-[pP]*|-[fF]*)
    #; void --hash password known args
    OPTIND=1
    if [[ "$#" -gt 1 ]]; then
      arg=$2; [[ "${arg:0:1}" != '-' ]] && OPTIND=2
    fi
    shift $((OPTIND -1))
    ;;
  -[mM]*|--submodule)
    git submodule sync && git submodule update --init --recursive --force;;
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
    composer_args="require --no-interaction"
    ;;
  -[aA]*|--apache )
    # shellcheck disable=SC2154
    echo -e "${green}Adding VirtualHost...${nc}"
    # shellcheck source=Scripts/config_a2ensite.sh
    bash -c "$TOPDIR/Scripts/config_a2ensite.sh $TOPDIR/app/webroot $TOPDIR/etc/apache2"
    ;;
  -[vV]*|--verbose )
    set -x
    echo "Passed params : ${BASH_SOURCE[*]} ${saved[*]}";;
    *) echo "Unknown parameter passed: ${BASH_SOURCE[0]} $1"; exit 1;;
esac; shift; done
#; update plugins and dependencies
bash -c "$TOPDIR/Scripts/composer.sh ${composer_args}"
slogger -st sed "Cake patches"
#; patches
patches "$APPPATH/Console/ShellDispatcher.php" "$APPPATH/Console/ConsoleOutput.php" "$APPPATH/../config/core.php"
