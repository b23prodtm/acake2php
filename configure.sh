#!/usr/bin/env bash
set -eu
source ./Scripts/lib/logging.sh
source ./Scripts/lib/shell_prompt.sh
source ./Scripts/lib/parsing.sh
openshift=$(parse_arg_exists "-[oO]+|--openshift"  $*)
docker=$(parse_arg_exists "--docker" $*)
pargs=$(parse_arg_trim "--docker|-[oO]+|--openshift" $*)
if [ $openshift 2> /dev/null ]; then
  slogger -st $0 "Bootargs...: ${pargs}"
  source ./Scripts/bootargs.sh $*
else
  slogger -st $0 "Locally Testing values, bootargs...: ${pargs}"
  source ./Scripts/fooargs.sh $*
fi
usage=("" \
"Usage: $0 [-m] [--openshift] [-c] [-h [-p password -s salt [-f filename]]]" \
"          [-m] [--openshift] [-c][[-d|--mig-database] [options]]" \
"          --openshift -d Using real environment variables to migrate database" \
"          -c,--const     Reset to app/webroot/php_cms/etc/constantes-template.properties" \
"          -h,--hash      Reset administrator password hash:" \
"               -p <password> -s <salt> [-f <save-filename>]" \
"                         Set administrator <password> with md5 <salt>. Optional file to save a shell script export." \
"          -m,--submodule Update sub-modules from Git" \
"          -d, --mig-database [options]" \
"                         Migrate Database (see $0 --mig-database --help)" \
"          --development  Install composer dependencies" \
"")
composer_args="require --no-interaction --update-no-dev"
saved=("$@")
export PHP_CMS_DIR=${PHP_CMS_DIR:-app/webroot/php_cms}
printf "PHP_CMS_DIR=%s in ~/.bash_profile or as environment variable." "${PHP_CMS_DIR}"
#; if the full set of the arguments exists, there won't be any prompt in the shell
while [[ "$#" > 0 ]]; do case $1 in
    -[cC]*|--const)
        shell_prompt "./Scripts/config_etc_const.sh" "${cyan}Step 1. Overwrite constantes.properties\n${nc}" "-Y"
        ;;
    -[hH]*|--hash)
        #; get hash password
        shell_prompt "./Scripts/config_etc_pass.sh ${*:2}" "${cyan}Step 2. Get a hashed password with encryption, PHP encrypts.\n${nc}" "-Y"
        ;;
    -[dD]*|--mig-database)
        #; Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Set automatic deployment with _100%_ unavailability
        #; If it starts a build, it automatically scales deployments down to zero, and deploys and scales up when it's finished to build.
        #; Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.
        #; [[-d|--mig-database] [-u]] argument fixes up : Error: Database connection "Mysql" is missing, or could not be created.
        shell_prompt "./migrate-database.sh ${docker} ${openshift} ${*:2}" "${cyan}Step 3. Migrate database\n${nc}" "-Y"
        break;;
    -[sS]*|-[pP]*|-[fF]*)
        #; void --hash password known args
        OPTIND=1
        if [[ "$#" > 1 ]]; then
          arg=$2; [[ ${arg:0:1} != '-' ]] && OPTIND=2
        fi
        shift $((OPTIND -1))
        ;;
    -[mM]*|--submodule)
        git submodule update --init --recursive --force;;
    --help )
        printf "%s\n" "${usage[@]}"
        exit 0;;
    -[oO]*|--openshift )
      ;;
    --docker )
      slogger -st docker "check database container status"
      bash -c "docker ps -q -f name=maria"
      ;;
    --development )
      composer_args="require --no-interaction"
      ;;
    -[vV]*|--verbose )
      set -x
      echo "Passed params : ${BASH_SOURCE[@]} ${saved[*]}";;
    *) echo "Unknown parameter passed: ${BASH_SOURCE[0]} $1"; exit 1;;
esac; shift; done
show_password_status "${DATABASE_USER}" "${MYSQL_ROOT_PASSWORD}" "is configuring ${openshift} ${docker}..."
echo -e "${green}Fixing some file permissions...${nc}"
[ $openshift 2> /dev/null ] && echo "None." || source ./Scripts/configure_tmp.sh
#; update plugins and dependencies
bash -c "./Scripts/composer.sh ${composer_args}"
