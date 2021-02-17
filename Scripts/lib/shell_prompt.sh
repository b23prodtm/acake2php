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
        # FIXME: bash -c might mask shell syntax errors (e.g. long filenames)
        # shellcheck disable=SC1090
        bash -c "$script" || log_failure_msg "FAILED $script"
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
  slogger -st "${FUNCNAME[0]}" "User ${1} (using password: $([ -z "$2" ] && echo "NO" || echo "YES")) $3..."
}
#; export -f show_password_status
cakephp() {
  CAKE="$TOPDIR/lib/Cake"
  APP="$TOPDIR/app"
  slogger -st "${FUNCNAME[0]}" "Cake 2.x patch ShellDispatcher.php"
  #; patches
  printf "%s\n" "s/\\\$dispatcher->_stop(\\(.*\\));/\\1;/g" > "${CAKE}/Console/ShellDispatcher.php.sed"
  printf "%s\n" "s/implode\\((\\\$styleInfo),(.*)\\)/implode\\(\\2,\\1\\)/g" > "${CAKE}/Console/ConsoleOutput.php.sed"
  files=("${CAKE}/Console/ShellDispatcher.php" "${CAKE}/Console/ConsoleOutput.php")
  for f in "${files[@]}"; do
    sed -i.old -E -f "$f.sed" "$f"
  done
  php -q "${APP}/Console/cake.php" -working "$APP" "$@"
}
#; export -f cakephp
docker_name() {
  [ "$#" -lt 1 ] && echo "Usage: ${FUNCNAME[0]} 'DOCKER_HUB_NAME'" && exit 1
  echo "$1" | awk -F/ '{ print $2 }' | awk -F: '{ print $1 }'
}
#; export -f docker_name
