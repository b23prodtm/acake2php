#!/usr/bin/env bash
[ $# -lt 3 ] && echo "Usage: $0 <port> <user@host> <container-name>" && exit 0
ssh -ttp "$1" "$2" "balena exec -it \$(balena-engine ps | grep ${3} | awk '{print \$1}') /bin/sh"
