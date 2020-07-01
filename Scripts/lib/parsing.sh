#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
banner=("" "[$0] BASH ${BASH_SOURCE[0]}" ""); printf "%s\n" "${banner[@]}"
#; colorize shell script
nc="\033[0m"
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
parse_sql_password() {
  [ $# -lt 3 ] && printf "Usage: %s <environment-variable> <description> -<arg val>|--<arg=val>\n" \
  "${FUNCNAME[0]}" \
  && exit 1
  evar=$1
  desc=$2
  shift 2
  # Transform long options to short ones
  while [ "$#" -gt 0 ]; do
      # shellcheck disable=SC2046
      case "$1" in
      -[pP]*|-[tT]*)
        parse_and_export "$1" "$evar" "$desc" "${@}"
        shift
        ;;
      --sql-password*) set -- $(echo "$1" \
      | awk 'BEGIN{ FS="[ =]+" }{ print "-p " $2 }') "${@:2}"
        parse_and_export "-p" "$evar" "$desc" "${@}"
        shift
        ;;
      --test-sql-password*) set -- $(echo "$1" \
      | awk 'BEGIN{ FS="[ =]+" }{ print "-t " $2 }') "${@:2}"
        parse_and_export "-t" "$evar" "$desc" "${@}"
        shift
        ;;
      *);;
    esac;shift
  done
}
#; export -f parse_sql_password
parse_arg_export() {
  [ $# -lt 3 ] && printf "%s\n" \
  "Usage: ${FUNCNAME[0]} <environment-variable> <description> -<arg> <val>" \
  && exit 1
  evar=$1
  desc=$2
  shift 2
  zval=$(echo "$@" | awk 'BEGIN{ FS="[ =]+" }{ print $2 }')
  while true; do case "$zval" in
    "")
      read -r -s -p "
Please, enter the $desc value now:
" zval
      ;;
    *)
      break;;
  esac; done
  eval "export ${evar}=${zval}"
}
#; export -f parse_arg_export
parse_arg_exists() {
  [ $# -eq 1 ] && return
  [ $# -lt 2 ] && printf "%s\n" \
  "Usage: ${FUNCNAME[0]} <match_case> list-or-\$*" \
  "Prints the argument index that's matched in the regex-arg-case (~ patn|patn2)" \
  && exit 1
  export arg_case=$1
  shift
  echo "$@" | awk 'BEGIN{FS=" "; ORS=" "; split(ENVIRON["arg_case"], a, "|")} {
    n=-1
    for(i in a) {
     for(f=1; f<=NF; f++) {
      if($f ~ a[i]) {
        n=f
        break
      }
      if(n != -1) break
     }
    }
  }
  END {
    if(n >= 0) print n
  }'
 unset arg_case
}
#; export -f parse_arg_exists()
parse_arg_trim() {
 [ $# -eq 1 ] && return
 [ $# -lt 2 ] && printf "%s\n" \
 "Usage: ${FUNCNAME[0]} <match_case> list-or-\$*" \
 "Prints the argument list that's not matched in the regex-arg-case (~ patn|patn2)" \
 && exit 1
  export arg_case=$1
  shift
  echo "$@" | awk 'BEGIN{FS=" "; ORS=" "; split(ENVIRON["arg_case"], a, "|"); n[0]=""} {
    for(f=1;f<=NF;f++) {
      n[f]=$f
      for(i in a) {
        if($f ~ a[i]) n[f]=""
      }
    }
  } END{
      for(f in n) {
        if(n[f] != "") print n[f]
      }
  }'
}

#; export -f parse_dom_host()
### -------------------------
# Only short options (e.g. -a -f) are supported.
# Long options must be transformed into short ones before.
# When an argument --name=Bob passes, transform into -n Bob:
#
#     set -- $(echo "$1" \
#     | awk 'BEGIN{ FS="[ =]+" }{ print "-n " $2 }') "${@:2}"
#     parse_and_export -n NAME "Set user name" "${@:2}"
#
# To continue arguments processing after a call to this function :
#
#     shift
#
parse_and_export() {
  [ $# -lt 4 ] && printf "%s\n" \
  "Usage: ${FUNCNAME[0]} <arg-name> <export-var> <description> <-arg list> " \
  && exit 1
  optstr=$1
  evar=$2
  desc=$3
  shift 3
  unset OPTIND
  while [ "$#" -gt 0 ]; do
    OPTIND=$(("$(parse_arg_exists "$optstr" "$@")" +1))
    if [ "$OPTIND" -gt 0 ]; then
      shift $((OPTIND -1))
      parse_arg_export "$evar" "${desc}" "-${optstr}" "${1:-}"
      [ -n "${1:-}" ] && OPTIND=$((OPTIND +1))
      break
    fi
    shift
  done
  export OPTIND
}
#; export -f parse_and_export()
