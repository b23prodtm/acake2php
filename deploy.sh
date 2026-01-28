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
REV=$(git -C .git rev-parse 2>/dev/null)
# ==================================================!!!!!!!!!!!!!!!LINE WRAPPED \
DOCKER_USER="${DOCKER_USER:-betothreeprod}" COLUMNS=0 LINES=0 SYSTEMD_NO_WRAP=0 \
# Fixes: Agent pid alive
pid="$SSH_AGENT_PID"

while kill -0 "$pid" 2>/dev/null; do
    echo "Agent $pid alive"
    sleep 1
done

echo "Agent $pid is gone"
# ======================
balena_deploy "${BASH_SOURCE[0]}" "$@"
[[ "$REV" -eq 0 ]] && git add docker-compose.yml
[[ "$REV" -eq 0 ]] && git commit -m "Deployment was updated"
