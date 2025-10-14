#!/usr/bin/env bash
rm -f deployment/images/mysqldb/conf.d/custom.cnf
# Fixes: unbound variables on ubuntu
DOCKER_USER="${DOCKER_USER:-betothreeprod}" COLUMNS=0 LINES=0 SYSTEMD_NO_WRAP=0 \
# Fixes: not inside a working tree
REV=$(git -C .git rev-parse 2>/dev/null)
[[ "$REV" -eq 0 ]] && git stash
balena_deploy "${BASH_SOURCE[0]}" "$@"
[[ "$REV" -eq 0 ]] && git stash pop
