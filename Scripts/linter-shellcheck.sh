#!/bin/bash -eo pipefail
IGNORE_STRING=SC1091,SC2034,SC2096,SC2155,SC1090,SC2096,SC2038
SHELLCHECK_OPTS=$(echo "-e ${IGNORE_STRING//,/ -e }")

export SHELLCHECK_OPTS="$SHELLCHECK_OPTS"

echo "Running shellcheck with the following SHELLCHECK_OPTS value..."
echo "SHELLCHECK_OPTS=$SHELLCHECK_OPTS"

find '.' -not -path '' \
  -type f -name '*.sh' | \
  xargs shellcheck --external-sources | \
  tee -a 'shellcheck.log'
