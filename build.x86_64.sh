#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
ln -s "x86_64.env" "x86_64.env"
cp -vf "${TOPDIR}/docker-compose.x86_64" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f docker-bake.hcl php-fpm \
  --set "*.platform=linux/amd64" \
  --push
