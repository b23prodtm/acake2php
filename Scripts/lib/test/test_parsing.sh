#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
FORMAT="[%s] %s %s (VALUE=%s)\n"
# During the test build, arguments were passed "inline" or "dollar-star" $*
# It turns out that bash shells passes arguments array or "dollar-array" $@.
# The difference's in layouts by printing list inline "$*" or column style "$@".

function test_args() {
  set -- --open --data "0x500" -d "0x44"
  printf "arg list: %s\n" "$*"

  parse_args "$@" <<EOF
flag P -o --open
option T -d --data
flag X -x --Xen
option Z -z --Zone
option Y -y --Yacht
end
EOF
  # shellcheck disable=SC2059
  if [ ${#T} -gt 0 ]; then
    printf "$FORMAT" "SUCCESS" "1° match" "-d" "$T"
  else
    printf "$FORMAT" "FAIL" "1° match" "-d" "$T"
  fi
  set -- --open --data "0x500" -d "0x44"

  parse_args_lazy "$@" <<EOF
flag P -o --open
option T -d --data
flag X -x --Xen
option Z -z --Zone
option Y -y --Yacht
end
EOF
  # shellcheck disable=SC2059
  if [ ${#P} -gt 0 ]; then
    printf "$FORMAT" "SUCCESS" "2° match" "-o" "$P"
  else
    printf "$FORMAT" "FAIL" "2° match" "-o" "$P"    
  fi
}

function test_arg_trim() {
  set -- -o --data 0x500 -d 0x44 -remaining-arg
  printf "arg list: %s\n" "$*"

  # shellcheck disable=SC2046 
  set -- $(trim_args "$@" <<EOF
flag T -o --open
flag X -x --Xen
option Z -z --Zone
option Y -y --Yacht
end
EOF
  )
  # shellcheck disable=SC2059
  if [[ $(echo "$@" | wc -w) -eq $(echo "--data 0x500 -d 0x44 -remaining-arg" | wc -w) ]]; then
    printf "$FORMAT" "SUCCESS" "3° trim" "-o" "$*"
  else
    printf "$FORMAT" "FAIL" "3° trim" "-o" "$*"
  fi

# shellcheck disable=SC2046 
  set -- $(trim_args "$@" <<EOF
option P -d --data
flag X -x --Xen
option Z -z --Zone
option Y -y --Yacht
end
EOF
  )
  # shellcheck disable=SC2059
  if [[ $(echo "$@" | wc -w) -eq $(echo "-remaining-arg" | wc -w) ]]; then
    printf "$FORMAT" "SUCCESS" "4° trim" "-d" "$*"
  else
    printf "$FORMAT" "FAIL" "4° trim" "-d" "$*"
  fi
}

function test_parse_and_export() {
  set -- --pass "1st password" --test pass_one
  printf "arg list: %s\n" "$*"
  parse_and_export T -t --test "$@"
  # shellcheck disable=SC2059
  if [ "$T" = "pass_one" ]; then
    printf "$FORMAT"  "SUCCESS" "5° export" "-t" "$T"
  else
    printf "$FORMAT" "FAIL" "5° export" "-t" "$T"
  fi

  set -- -p "2nd password" -t pass_two
  printf "arg list: %s\n" "$*"

  parse_and_export P -p --pass "$@"
  # shellcheck disable=SC2059
  if [ "$P" = "2nd password" ]; then
    printf "$FORMAT" "SUCCESS" "6° export" "--pass" "$P"
  else
    printf "$FORMAT" "FAIL" "6° export" "--pass" "$P"
  fi
  unset P T
}

test=("test_args" "test_arg_trim" "test_parse_and_export")
for t in "${test[@]}"; do
  printf " - - - - TEST CASES : %s - - - -\n" "$t" && eval "$t"
  sleep 1
done
