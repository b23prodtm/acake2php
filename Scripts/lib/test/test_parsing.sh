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
  args=(-p "1st password" -t "pass_one")
  parse_and_export T -t --test "${args[@]}"
  # shellcheck disable=SC2059
  [ "$T" = "pass_one" ] \
  && printf "${Z[*]}"  "OK" "1° export" "-t" "$T" \
  || printf "${Z[*]}" "FAILED" "1° export" "-t" "$T"

  args=(-t "2nd password" "--pass" "pass_two")
  parse_and_export P -p --pass "${args[@]}"
  # shellcheck disable=SC2059
  [ "$P" = "pass_two" ] \
  && printf "${Z[*]}" "OK" "2° export" "--pass" "$P" \
  || printf "${Z[*]}" "FAILED" "2° export" "--pass" "$P"
  unset P T
}

function test_args() {
  args=(-d me "--open=9" --data)
  parse_args "${args[@]}" <<EOF
flag T -d --data
end
EOF

  # shellcheck disable=SC2059
  [ -n "$T" ] \
  && printf "${Z[*]}" "OK" "1° match" "$T" 0 \
  || printf "${Z[*]}" "FAILED" "1° match" "$T" 0
  
  parse_args "${args[@]}" <<EOF
option P -o --open
end
EOF

  # shellcheck disable=SC2059
  [ -n "$P" ] \
  && printf "${Z[*]}" "OK" "2° match" "$P" 2 \
  || printf "${Z[*]}" "FAILED" "2° match" "$P" 2
}

function test_arg_trim() {
  args=(--open --data "0x500" -d "0x44")
  parse_args "${args[@]}" <<EOF
option DATA -d --data
end
EOF
  T="${args[*]}"
  # shellcheck disable=SC2059
  [[ $(echo "$T" | wc -w) -eq $(echo "--open" | wc -w) ]] \
  && printf "${Z[*]}" "OK" "1° trim" "--data" "$DATA" \
  || printf "${Z[*]}" "FAILED" "1° trim" "--data" "$DATA"

  args=(--open --data "0x500" -o)
  parse_args "${args[@]}" <<EOF
flag OPEN -o --open
end
EOF
  P="${args[*]}"
  # shellcheck disable=SC2059
  [[ $(echo "$P" | wc -w) -eq $(echo "-d me --data 0X500" | wc -w) ]] \
  && printf "${Z[*]}" "OK" "2° trim" "--open" "$OPEN" \
  || printf "${Z[*]}" "FAILED" "2° trim" "--open" "$OPEN"
}
test=("test_args" "test_arg" "test_arg_trim" "test_parse_and_export")
for t in "${test[@]}"; do
  printf "TEST CASES : %s\n" "$t" && eval "$t"
done
sleep 2
