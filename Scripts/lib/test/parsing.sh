#!/usr/bin/env bash
set -e
source ./Scripts/lib/parsing.sh
Z=("[%s] %s %s(OPTIND=%s)\n")
# During the test build, arguments were passed "inline" or "dollar-star" $*
# It turns out that bash shells passes arguments array or "dollar-array" $@.
# The difference's in layouts by printing list inline "$*" or column style "$@".
function test_parse_and_export() {
  args=("t" "T" "1st password" "-t" "pass_one")
  parse_and_export "${args[@]}"
  [ "$T" = "pass_one" ] \
  && printf "$Z"  "OK" "1° export" $FUNCNAME $OPTIND || printf "$Z" "FAILED" "1° export" $FUNCNAME $OPTIND

  args=("p" "P" "2nd password" "-t" "-p" "pass_two")
  parse_and_export "${args[@]}"
  [ "$P" = "pass_two" ] \
  && printf "$Z" "OK" "2° export" $FUNCNAME $OPTIND || printf "$Z" "FAILED" "2° export" $FUNCNAME $OPTIND
}

function test_parse_sql_password() {
  args=("P" "password one" "-p" "pass_one" "-foo" "arg")
  parse_sql_password "${args[@]}"
  [ "$P" = "pass_one" ] \
  && printf "$Z" "OK" "1° password" $FUNCNAME $OPTIND || printf "$Z" "FAILED" "1° password" $FUNCNAME $OPTIND

  args=("S" "password two" "--sql-password=pass_two" "-foo" "arg")
  parse_sql_password "${args[@]}"
  [ "$S" = "pass_two" ] \
  && printf "$Z" "OK" "2° password" $FUNCNAME $OPTIND || printf "$Z" "FAILED" "2° password" $FUNCNAME $OPTIND
}

function test_arg_exists() {
  args=("-d" "me" "--open=9" "--data")
  T=$(parse_arg_exists "-d" $args)
  [ $T ] \
  && printf "$Z" "OK" "1° match" $FUNCNAME $OPTIND || printf "$Z" "FAILED" "1° match" $FUNCNAME $OPTIND

  P=$(parse_arg_exists "-d|--data" $args)
  [ $P ] \
  && printf "$Z" "OK" "2° match" $FUNCNAME $OPTIND || printf "$Z" "FAILED" "2° match" $FUNCNAME $OPTIND
}

function test_arg_trim() {
  args="-d me --open --data"
  T=$(parse_arg_trim "-d|--data" $args)
  [[ "${#T}" -eq $(echo "me --open" | wc -m) ]] \
  && printf "$Z" "OK" "1° trim" $FUNCNAME $OPTIND || printf "$Z" "FAILED" "1° trim" $FUNCNAME $OPTIND

  P=$(parse_arg_trim "--open" $args)
  [[ "${#P}" -eq $(echo "-d me --data" | wc -m) ]] \
  && printf "$Z" "OK" "2° trim" $FUNCNAME $OPTIND || printf "$Z" "FAILED" "2° trim" $FUNCNAME $OPTIND
}
