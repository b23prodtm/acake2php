#!/usr/bin/env bash
# Check OS we are running on.  NetworkManager only works on Linux.
if [[ "$OSTYPE" != "linux"* ]]; then
    echo "ERROR: This application only runs on Linux."
    if [[ "$OSTYPE" == "darwin"* ]]; then
        echo "WARNING: OSX is only supported for development/simulation."
    else
        exit 1
    fi
fi
REV=https://raw.githubusercontent.com/b23prodtm/vagrant-shell-scripts/b23prodtm-patch/ubuntu.sh
LNK=/usr/local/share/vagrant-shell-scripts/ubuntu.sh
mkdir -p $(dirname $LNK)
curl -sSL $REV -o $LNK
source $LNK
$*
