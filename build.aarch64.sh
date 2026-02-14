#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
ln -s "aarch64.env" ".env"
cp -vf "${TOPDIR}/docker-compose.aarch64" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f docker-bake.hcl php-fpm \
  --set "*.platform=linux/arm64" \
  --push
