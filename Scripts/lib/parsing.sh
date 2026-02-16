#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)

# parse_args:
#   Parse arguments inside a function without touching globals.
#   Supports:
#     - Flags:        -v  --verbose
#     - Key/Value:    -o X  --output=X  --output X
#     - Positionals:  stored in "$@"
#
# Usage inside a function:
#   parse_args "$@" <<EOF
#   flag VERBOSE -v --verbose
#   option OUT   -o --output
#   end
#   EOF
#
# After parsing:
#   $VERBOSE = 0/1
#   $OUT     = value or empty
#   "$@"     = remaining positional args
#
parse_args_lazy() {
    # Read spec from stdin
    # Format:
    #   flag   VAR  -s  --long
    #   option VAR  -s  --long
    #   end
    #
    # Output:
    #   Sets variables in caller scope using 'eval'
    #   Leaves positional args in "$@"

    # Temporary arrays
    local _flags=""
    local _opts=""
    # Read spec
    while read -r type var short long; do
        [ "$type" = "end" ] && break

        case "$type" in
            flag)
                _flags="$_flags $short:$long:$var"
                ;;
            option)
                _opts="$_opts $short:$long:$var"
                ;;
            *)
                printf "parse_args: invalid spec line: %s\n" "$type" >&2
                return 1
                ;;
        esac
    done

    # printf "Flags list: %s\n" "$_flags" >&2
    # printf "Options list: %s\n" "$_opts" >&2

    # Initialize all variables to empty/0
    for entry in $_flags; do
        var=${entry##*:}
        eval "$var=0"
    done
    for entry in $_opts; do
        var=${entry##*:}
        eval "$var=''"
    done

    # Now parse actual arguments
    _positional=""; local _s=""
    while [ $# -gt 0 ]; do
        arg=$1
        shift

        case "$arg" in
            --*=*)
                key=${arg%%=*}
                val=${arg#*=}
                ;;
            -*)
                key=$arg
                val=""
                ;;
            *)
                _positional="${_positional}${_s}${arg}"
                _s=" "  
                continue
                ;;
        esac

        # Match flags
        matched=0
        for entry in $_flags; do
            short=${entry%%:*}
            rest=${entry#*:}
            long=${rest%%:*}
            var=${rest#*:}

            if [ "$key" = "$short" ] || [ "$key" = "$long" ]; then
                eval "$var=1"
                matched=1
                break
            fi
        done
        [ "$matched" -eq 1 ] && continue

        # Match options
        for entry in $_opts; do
            short=${entry%%:*}
            rest=${entry#*:}
            long=${rest%%:*}
            var=${rest#*:}

            if [ "$key" = "$short" ] || [ "$key" = "$long" ]; then
                if [ -z "$val" ]; then
                    [ $# -eq 0 ] && {
                        printf "Missing value for %s\n" "$key" >&2
                        return 1
                    }
                    val=$1
                    shift
                fi
                eval "$var=\$val"
                matched=1
                break
            fi
        done

        if [ "$matched" -eq 0 ]; then
            _positional="${_positional}${_s}${arg}"
            _s=" "
        fi
    done
    set -- $_positional
}
#; export -f parse_args_lazy()

function parse_args() {
  parse_args_lazy "$@"
  if [ "${#_positional}" -gt 0 ]; then printf "%s\n" "Unkown argument(s): $_positional" >&2; exit 1; fi
  unset _positional
}
#; export -f parse_args()

#   Trim arguments inside a function without touching globals and returns it
#   Supports:
#     - Flags:        -v  --verbose
#     - Key/Value:    -o X  --output=X  --output X
#     - Resting args:  stored in "$@"
#
function trim_args() {
  parse_args_lazy "$@"
  echo -e "$_positional"
  unset _positional
}
#; export -f trim_args()

# parse_and_export:
# set and export variables from arg list or PROMPT if the value is not set
#    Supports short and long argument:
#       OPTION -s "--longOption" "arg-list"
#    Usage examples:
#      parse_and_export FILENAME -f "--file" $* (args: "-f afile.txt")
#      parse_and_export FILENAME -f "--file" $* (args: "-s --file=out")
#    After parsing:
#      FILENAME=afile.txt
#      FILENAME=out
parse_and_export() {
  [ $# -lt 4 ] && printf "%s\n" \
  "Usage: ${FUNCNAME[0]} <export-var> <arg-name> <long-arg-name> <-arg list> " >&2 \
  && exit 1
  local evar=$1
  local flag=$2
  local long=$3
  shift 3
  parse_args_lazy "$@" <<EOF
option $evar $flag $long
end
EOF
  unset _positional
  eval "export $evar"
}
#; export -f parse_and_export()
