#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname $(dirname $(dirname $(dirname "${BASH_SOURCE[0]}"))))" && pwd)
balena_deploy . x86_64 3 0
docker buildx bake -f docker-bake.hcl --service php-fpm \
  --set "*.platform=linux/amd64"
