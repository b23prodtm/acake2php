#!/usr/bin/env bash
### Paste Here latest File Revisions
REV=https://gist.githubusercontent.com/b23prodtm/5f96368412589223869bc659b30d263e/raw/78800a9fbd85b4583782f3f2a643e65a595fa4c4/docker_build.sh
sudo curl -sSL $REV -o /usr/local/bin/docker_build
sudo chmod 0755 /usr/local/bin/docker_build
source docker_build ${BASH_SOURCE[0]} "$@"
