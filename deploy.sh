#!/usr/bin/env bash
### Paste Here latest File Revisions
REV=https://gist.githubusercontent.com/b23prodtm/5f96368412589223869bc659b30d263e/raw/63652bbd3859767e52801371c196e463a288e9bf/balena_deploy.sh
sudo curl -sSL -o /usr/local/bin/balena_deploy $REV
sudo chmod 0755 /usr/local/bin/balena_deploy
source balena_deploy ${BASH_SOURCE[0]} "$@"
