#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)

#; export -f parse_arg_exists()
parse_arg_trim() {
 [ $# -eq 1 ] && return
 [ $# -lt 2 ] && printf "%s\n" \
 "Usage: ${FUNCNAME[0]} <match_case> list-or-\$*" \
 "Prints the argument list that's not matched in the regex-arg-case (~ patn|patn2)" \
 && exit 1
  export arg_case=$1
  shift
  echo "$@" | awk 'BEGIN{FS=" "; ORS=" "; split(ENVIRON["arg_case"], a, "|"); n[0]=""} {
    for(f=1;f<=NF;f++) {
      n[f]=$f
      for(i in a) {
        if($f ~ a[i]) n[f]=""
      }
    }
  } END{
      for(f in n) {
        if(n[f] != "") print n[f]
      }
  }'
}

#; export -f parse_dom_host()

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
parse_args() {
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
    _flags=""
    _opts=""

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
    _positional=""
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
                _positional="$_positional \"$arg\""
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
            printf "Unknown argument: %s\n" "$arg" >&2
            return 1
        fi
    done

    # Export positional args back to caller
    eval "set -- $_positional"
}
#; export -f parse_args()

### -------------------------
# parse_and_export -n NAME "--name" "$@"
#
parse_and_export() {
  [ $# -lt 4 ] && printf "%s\n" \
  "Usage: ${FUNCNAME[0]} <arg-name> <export-var> <long-arg-name> <-arg list> " \
  && exit 1
  flag=$1
  evar=$2
  long=$3
  shift 3
  parse_args "$@" <<EOF
option $evar $flag $long
end
EOF
  eval "export $evar"
}
#; export -f parse_and_export()
