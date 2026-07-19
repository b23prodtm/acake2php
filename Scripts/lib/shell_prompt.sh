#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
shell_prompt() {
  [ $# -lt 2 ] && printf "Usage: %s <script> <name> [-y|n] [args...]" "${FUNCNAME[0]}" && exit 1
  script="$1"
  title="$2"
  shift 2
  # Optional 3rd param controls prompt behavior, rest are args for the script
  answer="${1:-}"
  [ ${#answer} -gt 0 ] && shift 1
  while true; do
    # (1) prompt user, and read command line argument if no 3rd arg
    log_daemon_msg "Run ${title} ?..."
    case $answer in
      -[yY]*|noninteractive ) answer="Y" ;;
      -[nN]* ) answer="N" ;;
      * ) read -r -p "Do ${script} now (Y/N) ? " answer ;;
    esac
    # (2) run a script if the user answered Y (yes) or N (no) quit the script
    case $answer in
      [yY]* ) echo -e "Yes."
        "$script" "$@" || log_failure_msg "FAILED"
        break;;
      [nN]* ) echo -e "No.\n"
        break;;
      * ) log_failure_msg "Dude, just enter Y or N, please." ;;
    esac
  done
}
#; export -f shell_prompt
show_password_status() {
  [ "$#" -lt 3 ] && echo "Usage: ${FUNCNAME[0]} '<VAR_USER>' '<VAR_PASSWORD>' <action-description>" && exit 1
  log_daemon_msg "$0 User ${1} (using password: $([ -z "$2" ] && echo "NO" || echo "YES")) $3..."
}
#; export -f show_password_status
cakephp() {
  "$TOPDIR"/bin/cake
}
#; export -f cakephp
docker_name() {
  [ "$#" -lt 1 ] && echo "Usage: ${FUNCNAME[0]} 'DOCKER_HUB_NAME'" && exit 1
  echo "$1" | awk -F/ '{ print $2 }' | awk -F: '{ print $1 }'
}
#; export -f docker_name
