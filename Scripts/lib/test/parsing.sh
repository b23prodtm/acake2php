#!/usr/bin/env bash
set -e
source ./Scripts/lib/parsing.sh

function test_parse_and_export() {
  args=("t" "T" "1st password" "-t pass_one")
  printf "${args[*]} "
  parse_and_export "${args[@]}"
  [ ! -z $T ] && [ "$T" = "pass_one" ] \
  && printf "[${FUNCNAME[0]}] 1° password OK\n" || printf "[${FUNCNAME[0]}] 1° password FAILED\n"

  args=("p" "P" "2nd password" "-t -p pass_two")
  printf "${args[*]} "
  parse_and_export "${args[@]}"
  [ ! -z $P ] && [ "$P" = "pass_two" ] \
  && printf "[${FUNCNAME[0]}] 2° password OK\n" || printf "[${FUNCNAME[0]}] 2° password FAILED\n"
}

function test_parse_sql_password() {
    args=("P" "password one" "-p pass_one")
    printf "${args[*]} "
    parse_sql_password "${args[@]}"
    [ ! -z $P ] && [ "$P" = "pass_one" ] \
    && printf "[${FUNCNAME[0]}] 1° password OK\n" || printf "[${FUNCNAME[0]}] 1° password FAILED\n"

    args=("S" "password two" "--sql-password=pass_two")
    printf "${args[*]} "
    parse_sql_password "${args[@]}"
    [ ! -z $S ] && [ "$S" = "pass_two" ] \
    && printf "[${FUNCNAME[0]}] 2° password OK\n" || printf "[${FUNCNAME[0]}] 2° password FAILED\n"
}
