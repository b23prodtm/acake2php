#!/usr/bin/env bash
set -e
shell_prompt() {
  [ $# -lt 2 ] && echo "Usage: $FUNCNAME <file> <name> [-y|n]" && exit 1
  script=$1
  title=$2
  while true; do
          # (1) prompt user, and read command line argument if no 3rd arg
          echo -e "${cyan}Run ${title} ?...\n${nc}"
          answer=$3
          case $answer in
                 -[yY]*|noninteractive ) answer="Y";;

                 -[nN]* ) answer="N";;

                 * )
                          read -p "Do ${script} now (Y/N) ? " answer;;
          esac
          # (2) run a script if the user answered Y (yes) or N (no) quit the script
          case $answer in
                 [yY]* ) echo -e "Yes.\n"
                          [[ -f "$script" ]] && source $script || bash -c "$script"
                          break;;

                 [nN]* ) echo -e "No.\n"
                          break;;

                 * )     echo -e "${red}Dude, just enter Y or N, please.\n${nc}";;
          esac
  done
}
#; export -f shell_prompt
show_password_status() {
  [ "$#" -lt 3 ] && echo "Usage: $FUNCNAME '<VAR_USER>' '<VAR_PASSWORD>' <action-description>" && exit 1
  slogger -st $FUNCNAME "User ${green}${1}${nc} (using password:${orange} $([ -z $2 ] && echo "NO" || echo "YES")${nc}) $3...\n"
}
#; export -f show_password_status
cakephp() {
  CAKE=$(dirname $(dirname $(dirname $BASH_SOURCE)))/lib/Cake
  APP=$(dirname $(dirname $(dirname $BASH_SOURCE)))/app
  slogger -st $FUNCNAME "Cake 2.x patch ShellDispatcher.php"
  sed -i.orig -e "s/\$dispatcher->_stop\((.*)\);/\\1;/g" "${CAKE}"/Console/ShellDispatcher.php
  exec php -q "${APP}"/Console/cake.php -working "$APP" "$@"
}
