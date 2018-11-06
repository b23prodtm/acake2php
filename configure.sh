#!/bin/sh
#;
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"

echo "${green}Fixing some file permissions...${nc}"
sh ./Scripts/configure_tmp.sh
#; arguments are ./configure.sh -Y|-N [-p password -s salt -f filename]
#; if the full set of the arguments exists, there won't be any prompt in the shell

sh ./Scripts/shell_prompt.sh "./Scripts/config_etc_const.sh" "${cyan}Step 1. Overwrite constantes.properties\n${nc}" $1
#; get hash password argv are -p password -s salt -f filename (resp in order)
sh ./Scripts/shell_prompt.sh "./Scripts/config_etc_pass.sh ${4} ${5} ${6} ${7} ${8} ${9}" "${cyan}Step 2. Get a hashed password with encryption, PHP encrypts.\n${nc}" $2

# Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Set automatic deployment with _100%_ unavailability
# while deploying and _0_ surge pod in deployment advanced edit configuration tab.
# If it starts a build, it automatically scales deployments down to zero, and deploys and scales up when it's finished to build.
# Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.

sh ./Scripts/shell_prompt.sh "migrate-database.sh -y -y -y" "${cyan}Step 3. Migrate database\n${nc}" $3
