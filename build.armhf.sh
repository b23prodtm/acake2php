#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
ln -s "armhf.env" ".env"
cp -vf "${TOPDIR}/docker-compose.armhf" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f docker-bake.hcl php-fpm \
  --set "*.platform=linux/arm/v7" \
  --push
