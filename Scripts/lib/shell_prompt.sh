#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
shell_prompt() {
  [ $# -lt 2 ] && printf "Usage: %s <file> <name> [-y|n]" "${FUNCNAME[0]}" && exit 1
  script=$1
  title=$2
  while true; do
    # (1) prompt user, and read command line argument if no 3rd arg
    # shellcheck disable=SC2154
    log_daemon_msg "Run ${title} ?..."
    answer=$3
    case $answer in
      -[yY]*|noninteractive ) answer="Y"
        ;;
      -[nN]* ) answer="N"
        ;;
      * ) read -r -p "Do ${script} now (Y/N) ? " answer
        ;;
    esac
    # (2) run a script if the user answered Y (yes) or N (no) quit the script
    case $answer in
      [yY]* ) echo -e "Yes."
        # shellcheck disable=SC1090
        exec $script || true
        break;;
      [nN]* ) echo -e "No.\n"
        break;;
      * ) log_failure_msg "Dude, just enter Y or N, please."
      ;;
    esac
  done
}
#; export -f shell_prompt
show_password_status() {
  [ "$#" -lt 3 ] && echo "Usage: ${FUNCNAME[0]} '<VAR_USER>' '<VAR_PASSWORD>' <action-description>" && exit 1
  slogger -st "${FUNCNAME[0]}" "User ${1} (using password: $([ -z $2 ] && echo "NO" || echo "YES")) $3..."
}
#; export -f show_password_status
cakephp() {
  CAKE="$TOPDIR/lib/Cake"
  APP="$TOPDIR/app"
  slogger -st "${FUNCNAME[0]}" "Cake 2.x patch ShellDispatcher.php"
  sed -i.orig -e "s/\$dispatcher->_stop\((.*)\);/\\1;/g" "${CAKE}/Console/ShellDispatcher.php"
  exec php -q "${APP}/Console/cake.php" -working "$APP" "$@"
}
