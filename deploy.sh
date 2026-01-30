#!/usr/bin/env bash
if ! command -v balena > /dev/null; then
   printf "%s\n" "balena client not found, install it along with NodeJS 22 (https://nvm.sh)"
   printf "%s\n" "https://github.com/balena-io/balena-cli/blob/master/INSTALL-LINUX.md"
   printf "%s\n" "You must be logged in"
   printf "%s\n" "[ \"$\(command -v podman\)\" ] && alias docker=podman; docker login"
   printf "%s\n" "balena login"
fi
if ! command -v balena_deploy > /dev/null; then
   printf "%s\n" "balena cloud apps not found, install it"
   printf "%s\n" "sudo npm install -g yarn"
   printf "%s\n" "yarn"
   printf "%s\n" "sudo npm link balena-cloud-apps"
   exit 0;
fi
rm -f deployment/images/mysqldb/conf.d/custom.cnf
# Fixes: unbound variables on ubuntu
REV=$(git rev-parse HEAD 2>/dev/null)

balena_deploy "${BASH_SOURCE[0]}" "$@"

if [ ${#REV} -gt 0 ]; then
    git add docker-compose.yml
    git commit -m "Deployment was updated" || true
fi

