#!/bin/bash
source ./Scripts/bootargs.sh

#; arguments are
args=$*
#; if the full set of the arguments exists, there won't be any prompt in the shell
while [[ "$#" > 0 ]]; do case $1 in
    -[cC]*|--const)
        source ./Scripts/shell_prompt.sh "./Scripts/config_etc_const.sh" "${cyan}Step 1. Overwrite constantes.properties\n${nc}" '-Y'
        ;;
    -[hH]*|--hash)
    #; get hash password
        source ./Scripts/shell_prompt.sh "./Scripts/config_etc_pass.sh ${args}" "${cyan}Step 2. Get a hashed password with encryption, PHP encrypts.\n${nc}" '-Y'
        ;;
    -[dD]*|--mig-database)
#; Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Set automatic deployment with _100%_ unavailability
#; If it starts a build, it automatically scales deployments down to zero, and deploys and scales up when it's finished to build.
#; Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.
#; [[-d|--mig-database] [-y]] argument fixes up : Error: Database connection "Mysql" is missing, or could not be created.
        shift
        source ./Scripts/shell_prompt.sh "migrate-database.sh $*" "${cyan}Step 3. Migrate database\n${nc}" '-Y'
        ;;
    -[sS]*|-[pP]*|-[fF]*)
        shift;;
    -[mM]*|--submodule)
        git submodule update --init --recursive --force;;
    --help )
          echo "./configure.sh [-m] [-c] [-h [-p password -s salt [-f filename]]] [[-d|--mig-database] [-y]]
              -c,--const Reset to app/webroot/php_cms/etc/constantes-template.properties
              -h,--hash Reset administrator password hash
                  -p <password> -s <salt> [-f <save-filename>]
              -d Migrate Database (see ./migrate-database.sh --help)
              -m,--submodule Update sub-modules from Git"
              exit 0;;
    *) echo "Unknown parameter passed: $1"; exit 1;;
esac; shift; done
#; update plugins and dependencies
source ./Scripts/composer.sh
echo -e "${green}Fixing some file permissions...${nc}"
source ./Scripts/configure_tmp.sh
