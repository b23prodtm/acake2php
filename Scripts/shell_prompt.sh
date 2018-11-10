#!/bin/sh
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
                        source $script
                        exit;;

               [nN]* ) echo "No.\n"
                        exit;;

               * )     echo "${red}Dude, just enter Y or N, please.\n${nc}";;
        esac
done
