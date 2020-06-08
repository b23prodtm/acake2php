#!/usr/bin/env bash
rm -f mysqldb/conf.d/custom.cnf
balena_deploy ${BASH_SOURCE[0]} "$@"
