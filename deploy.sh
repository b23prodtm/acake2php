#!/usr/bin/env bash
rm -f deployment/images/mysqldb/conf.d/custom.cnf
# Fixes: unbound variables on ubuntu
DOCKER_USER="${DOCKER_USER:-betothreeprod}" COLUMNS=0 LINES=0 SYSTEMD_NO_WRAP=0 \
git stash
balena_deploy "${BASH_SOURCE[0]}" "$@"
git stash pop && git stash
. init_functions .
log_daemon_msg "Use  git stash apply && git commit -a  to commit the last deployment settings"
