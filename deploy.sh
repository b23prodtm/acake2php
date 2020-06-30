#!/usr/bin/env bash
rm -f mysqldb/conf.d/custom.cnf
# Fixes: unbound variables on ubuntu
DOCKER_USER="${DOCKER_USER:-betothreeprod}" COLUMNS=0 LINES=0 SYSTEMD_NO_WRAP=0 \
balena_deploy ${BASH_SOURCE[0]} "$@"
