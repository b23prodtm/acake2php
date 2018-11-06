#!/bin/sh
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
script=$1
title=$2
while true
do
        # (1) prompt user, and read command line argument
        echo "${cyan}Run ${title} ?...\n${nc}"
        answer=$3
        case $answer in
               -[yY]* ) echo "Yes.\n"
                        answer="Y";;

               -[nN]* ) echo "No.\n"
                        break;;

               * )
                        read -p "Do ${script} now (Y/N) ? " answer;;
        esac
        case $answer in
               [yY]* ) echo "Yes.\n"
                        sh $script
                        exit;;

               [nN]* ) echo "No.\n"
                        exit;;

               * )     echo "${red}Dude, just enter Y or N, please.\n${nc}";;
        esac
done
