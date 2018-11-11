#!/bin/bash
source ./Scripts/bootargs.sh

echo -e "${green}Fixing some file permissions...${nc}"
source ./Scripts/configure_tmp.sh
#; arguments are ./configure.sh [-Y -Y -Y|-N -N -N|...] [-y] [-p password -s salt -f filename]
#; if the full set of the arguments exists, there won't be any prompt in the shell
source ./Scripts/shell_prompt.sh "./Scripts/config_etc_const.sh" "${cyan}Step 1. Overwrite constantes.properties\n${nc}" $1
#; get hash password argv are -p password -s salt -f filename (resp in order)
args=$@
source ./Scripts/shell_prompt.sh "./Scripts/config_etc_pass.sh ${args}" "${cyan}Step 2. Get a hashed password with encryption, PHP encrypts.\n${nc}" $2

# Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Set automatic deployment with _100%_ unavailability
# If it starts a build, it automatically scales deployments down to zero, and deploys and scales up when it's finished to build.
# Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.
# $4 [-y] argument fixes up : Error: Database connection "Mysql" is missing, or could not be created.
source ./Scripts/shell_prompt.sh "migrate-database.sh -y -y -y ${4}" "${cyan}Step 3. Migrate database\n${nc}" $3
