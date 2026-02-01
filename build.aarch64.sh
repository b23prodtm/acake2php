#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
balena_deploy . aarch64 3 0
docker buildx bake -f docker-bake.hcl php-fpm \
  --set "*.platform=linux/arm64" --push
