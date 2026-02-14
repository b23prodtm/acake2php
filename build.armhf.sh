#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
ln -s "${TOPDIR}/armhf.env" armhf.env
cp -vf "${TOPDIR}/docker-compose.${BALENA_ARCH}" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f docker-bake.hcl php-fpm \
  --set "*.platform=linux/arm/v7" \
  --push
