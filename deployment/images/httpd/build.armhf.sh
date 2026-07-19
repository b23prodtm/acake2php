#!/usr/bin/env bash
set -eu
TOPDIR="$(cd "$(dirname "${BASH_SOURCE[0]}"/../../..)" && pwd)"
ln -sf "${TOPDIR}/armhf.env" "${TOPDIR}/.env"
ln -sf "${TOPDIR}/docker-compose.armhf" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f "${TOPDIR}/docker-compose.yml" -f "${TOPDIR}/docker-bake.hcl" httpd \
  --set "*.platform=linux/arm/v7" \
  --push
