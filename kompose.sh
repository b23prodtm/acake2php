#!/usr/bin/env bash
# Check OS we are running on.  NetworkManager only works on Linux.
REV=https://github.com/kubernetes/kompose/releases/download/v1.21.0/kompose-linux-amd64
if [[ "$OSTYPE" != "linux"* ]]; then
    if [[ "$OSTYPE" == "darwin"* ]]; then
        echo "WARNING: OSX is only supported for development/simulation."
        REV=https://github.com/kubernetes/kompose/releases/download/v1.21.0/kompose-darwin-amd64
    else
      echo "ERROR: This application only runs on Linux."
      exit 1
    fi
fi
curl -L $REV -o kompose
chmod 0755 kompose
sudo mv -f kompose /usr/local/bin
source common.env
for d in ${BALENA_PROJECTS[@]}; do
  rm -fv $d/.env
done
exec kompose $*
