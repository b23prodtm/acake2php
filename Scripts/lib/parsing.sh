#!/usr/bin/env bash
set -e
#; colorize shell script
nc="\033[0m"
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
parse_sql_password() {
  [ $# -lt 3 ] && echo "Usage: $FUNCNAME <environment-variable> <description> -<arg val>|--<arg=val>" && exit 1
  evar=$1
  desc=$2
  shift 2
  # Transform long options to short ones
  OPTIND_2=1
  while [ "$#" -gt 0 ]; do
    case "$1" in
      -[pP]*|--sql-password*) set -- $(echo "$1" \
      | awk 'BEGIN{ FS="[ =]+" }{ print "-p " $2 }') "${@:2}"
        parse_and_export "p" $evar "$desc" "${@}"
        OPTIND_2=$((OPTIND_2 + $((OPTIND -1))))
        break
        ;;
      -[tT]*|--test-sql-password*) set -- $(echo "$1" \
      | awk 'BEGIN{ FS="[ =]+" }{ print "-t " $2 }') "${@:2}"
        parse_and_export "t" $evar "$desc" "${@}"
        OPTIND_2=$((OPTIND_2 + $((OPTIND -1))))
        break
        ;;
      *) OPTIND_2=$((OPTIND_2 + 1))
        printf "%s arg #%s is %s\n" $FUNCNAME $((OPTIND_2 -1)) $1;;
    esac
    shift;
  done
  export OPTIND=$((OPTIND_2))
}
#; export -f parse_sql_password
parse_arg_export() {
  [ $# -lt 3 ] && echo "Usage: $FUNCNAME <environment-variable> <description> -<arg> <val>" && exit 1
  evar=$1
  desc=$2
  shift 2
  zval=$(echo "$@" | awk 'BEGIN{ FS="[ =]+" }{ print $2 }')
  while true; do case "$zval" in
    "")
      read -sp "
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
  [ $# -lt 2 ] && echo "Usage: $FUNCNAME <match_case> list-or-\$*
Prints the index of the item that's matched in the list (regexpression pattern)" && exit 1
  arg_case=$1
  shift
  export ARGS="$@"
  echo $arg_case | awk 'BEGIN{FS="|"; ORS=" "; split(ENVIRON["ARGS"], a, " ")} {
  n=-1
  for(i=0; ++i in a;) {
    for(c=1;c<=NF;c++) {
      if(a[i] ~ $c) n=i
    }
  }
}
END {
  if(n >= 0) print a[n]
}'
}
#; export -f parse_arg_exists()
parse_arg_trim() {
 [ $# -eq 1 ] && return
 [ $# -lt 2 ] && echo "Usage: $FUNCNAME <match_case_regexp> list-or-\$*
Prints the list without the items that's matched (regexpression pattern)" && exit 1
  match_case_regexp=$1
  shift
  export ARGS="$@"
  echo $match_case_regexp | awk 'BEGIN{FS="|"; ORS=" "; split(ENVIRON["ARGS"], a, " ")} {
  n[0]=-1
  for(i=0; ++i in a;) {
    for(c=1;c<=NF;c++) {
      if(a[i] ~ $c) n[i]=a[i]
    }
  }
}
END {
  p=-1
  for(i=0; ++i in a;) {
      for(q in n) {
        if(i == q) p=n[q]
      }
      if(a[i] != p) print a[i]
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
  [ $# -lt 4 ] && echo "Usage: $FUNCNAME <arg-name> <export-var> <description> <argument-list> " && exit 1
  optstr=$1
  evar=$2
  desc=$3
  shift 3
  OPTIND=1
  while getopts "${optstr}:" optchar "$@"; do
    case "${optchar}" in
      "${optstr}") parse_arg_export $evar "${desc}" "-${optchar}" ${OPTARG}
      break;;
      *) if [ "$OPTERR" != 1 ]; then echo "Non-option argument: '-${OPTARG}'" >&2; fi;;
    esac
  done; shift $((OPTIND -1));
  export OPTIND=$((OPTIND -1))
}
#; export -f parse_and_export()
