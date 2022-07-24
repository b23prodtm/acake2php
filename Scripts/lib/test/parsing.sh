#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
Z=("[%s] %s %s(OPTIND=%s)\n")
# During the test build, arguments were passed "inline" or "dollar-star" $*
# It turns out that bash shells passes arguments array or "dollar-array" $@.
# The difference's in layouts by printing list inline "$*" or column style "$@".
function test_parse_and_export() {
  args=(-t T "1st password" -t "pass_one")
  parse_and_export "${args[@]}"
  # shellcheck disable=SC2059
  [ "$T" = "pass_one" ] \
  && printf "${Z[*]}"  "OK" "1° export" "$T" $OPTIND \
  || printf "${Z[*]}" "FAILED" "1° export" "$T" $OPTIND

  args=(-p P "2nd password" -t "-p" "pass_two")
  parse_and_export "${args[@]}"
  # shellcheck disable=SC2059
  [ "$P" = "pass_two" ] \
  && printf "${Z[*]}" "OK" "2° export" "$P" $OPTIND \
  || printf "${Z[*]}" "FAILED" "2° export" "$P" $OPTIND
  unset P T
}

function test_parse_sql_password() {
  args=(P "password one" "-p" "pass_one" "--foo=arg")
  parse_sql_password "${args[@]}"
  # shellcheck disable=SC2059
  [ "$P" = "pass_one" ] \
  && printf "${Z[*]}" "OK" "1° password" "$P" $OPTIND \
  || printf "${Z[*]}" "FAILED" "1° password" "$P" $OPTIND

  args=("S" "password two" "-f" "arg" "--test-sql-password=pass_two")
  parse_sql_password "${args[@]}"
  # shellcheck disable=SC2059
  [ "$S" = "pass_two" ] \
  && printf "${Z[*]}" "OK" "2° password" "$S" $OPTIND \
  || printf "${Z[*]}" "FAILED" "2° password" "$S" $OPTIND
  unset P S
}

function test_arg_exists() {
  args=(-d me "--open=9" --data)
  T=$(parse_arg_exists "-d" "${args[@]}")
  # shellcheck disable=SC2059
  [ -n "$T" ] \
  && printf "${Z[*]}" "OK" "1° match" "$T" $OPTIND \
  || printf "${Z[*]}" "FAILED" "1° match" "$T" $OPTIND

  P=$(parse_arg_exists "-d|--data" "${args[@]}")
  # shellcheck disable=SC2059
  [ -n "$P" ] \
  && printf "${Z[*]}" "OK" "2° match" "$P" $OPTIND \
  || printf "${Z[*]}" "FAILED" "2° match" "$P" $OPTIND
}

function test_arg() {
  args=(-d me --open --data)
  T=$(parse_arg "-d|--data" "${args[@]}")
  # shellcheck disable=SC2059
  [ "$T" = "-d" ] \
  && printf "${Z[*]}" "OK" "1° parse" "$T" $OPTIND \
  || printf "${Z[*]}" "FAILED" "1° parse" "$T" $OPTIND

  P=$(parse_arg "--open" "${args[@]}")
  # shellcheck disable=SC2059
  [ "$P" = "--open" ] \
  && printf "${Z[*]}" "OK" "2° parse" "$P" $OPTIND \
  || printf "${Z[*]}" "FAILED" "2° parse" "$P" $OPTIND
}

function test_arg_trim() {
  args=(-d me --open --data)
  T=$(parse_arg_trim "-d|--data" "${args[@]}")
  # shellcheck disable=SC2059
  [[ $(echo "$T" | wc -w) -eq $(echo "me --open" | wc -w) ]] \
  && printf "${Z[*]}" "OK" "1° trim" "$T" $OPTIND \
  || printf "${Z[*]}" "FAILED" "1° trim" "$T" $OPTIND

  P=$(parse_arg_trim "--open" "${args[@]}")
  # shellcheck disable=SC2059
  [[ $(echo "$P" | wc -w) -eq $(echo "-d me --data" | wc -w) ]] \
  && printf "${Z[*]}" "OK" "2° trim" "$P" $OPTIND \
  || printf "${Z[*]}" "FAILED" "2° trim" "$P" $OPTIND
}
test=("test_arg_exists" "test_arg" "test_arg_trim" "test_parse_and_export" "test_parse_sql_password")
for t in "${test[@]}"; do
  printf "TEST CASES : %s\n" "$t" && eval "$t"
done
sleep 2
