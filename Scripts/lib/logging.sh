#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
. init_functions .
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
parse_args "$@" <<EOF
flag runner -r --runner
end
EOF

if [ -n "$runner" ]; then
  export CAKEPHP_DEBUG_LEVEL=1
  # shellcheck source=bootargs.sh
  . "${TOPDIR}/Scripts/bootargs.sh" "$@"
else
  export CAKEPHP_DEBUG_LEVEL=2
  # shellcheck source=fooargs.sh
  . "${TOPDIR}/Scripts/fooargs.sh" "$@"
fi
#; Make logs folders available
mkdir -p "${MYPHPCMS_LOG}"
function new_cake_log() {
  a="$(new_log)"
  ln -sf "$a" "$(basename "$a")"
}
