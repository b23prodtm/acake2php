#!/bin/bash
set -e
script=$1
title=$2
while true; do
        # (1) prompt user, and read command line argument if no 3rd arg
        echo -e "${cyan}Run ${title} ?...\n${nc}"
        answer=$3
        case $answer in
               -[yY]* ) answer="Y";;

               -[nN]* ) answer="N";;

               * )
                        read -p "Do ${script} now (Y/N) ? " answer;;
        esac
        # (2) run a script if the user answered Y (yes) or N (no) quit the script
        case $answer in
               [yY]* ) echo -e "Yes.\n"
                        source $script
                        break;;

               [nN]* ) echo -e "No.\n"
                        break;;

               * )     echo -e "${red}Dude, just enter Y or N, please.\n${nc}";;
        esac
done
