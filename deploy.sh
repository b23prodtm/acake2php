#!/usr/bin/env bash
if [ ! $(command -v balena > /dev/null) ]; then
   printf "%s\n" "balena client not found, install it along with NodeJS 22 (https://nvm.sh)"
   printf "%s\n" "sudo npm install balena-cli --global --omit=dev --unsafe-perm"
fi
if [ ! $(command -v balena_deploy > /dev/null) ]; then
   printf "%s\n" "balena cloud apps not found, install it with"
   printf "%s\n" "sudo npm install -g balena-cloud-apps"
fi
rm -f deployment/images/mysqldb/conf.d/custom.cnf
# Fixes: unbound variables on ubuntu
REV=$(git -C .git rev-parse 2>/dev/null)
# ==================================================!!!!!!!!!!!!!!!LINE WRAPPED \
DOCKER_USER="${DOCKER_USER:-betothreeprod}" COLUMNS=0 LINES=0 SYSTEMD_NO_WRAP=0 \
 balena_deploy "${BASH_SOURCE[0]}" "$@"
[[ "$REV" -eq 0 ]] && git add docker-compose.yml
[[ "$REV" -eq 0 ]] && git commit -m "Deployment was updated"
