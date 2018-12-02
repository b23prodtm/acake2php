#!/bin/bash
set -e
parse_sql_password() {
  [ $# -lt 2 ] && echo "Usage: $0 -p|-t|--*sql-password*|*=<password> <var-name> <name>" && return $FALSE
  pass=$(echo $1 | cut -f 2 -d '=')
  while true; do case "$pass" in
    *sql-password|-[pPtT]|"")
      read -sp "
Please, enter the $3 SQL password now:
" pass;;
    *)
      break;;
  esac; done
  export $2=$pass
}
#; export -f parse_sql_password
parse_arg_exists() {
  [ $# -eq 0 ] && echo "Usage: $0 <argument-case> arguments-list"
  arg1=$(echo $1 | cut -f 1 -d '|')
  arg2=$(echo $1 | cut -f 2 -d '|')
  shift
  count=1
  while [[ "$#" > 0 ]]; do case $1 in
    $arg1|$arg2 ) echo $count && break;;
    *) ;;
  esac; count=$count+1; shift; done
}
#; export -f parse_arg_exists()
