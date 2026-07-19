#!/usr/bin/env bash
set -eu
TOPDIR="$(cd "$(dirname \
"$(dirname "${BASH_SOURCE[0]}")")" && pwd)"
ln -sf "${TOPDIR}/armhf.env" "${TOPDIR}/.env"
balena_deploy "${TOPDIR}" armhf 3 0
ln -sf "${TOPDIR}/docker-compose.armhf" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f "${TOPDIR}/docker-compose.yml" -f "${TOPDIR}/docker-bake.hcl" balena-storage \
  --set "*.platform=linux/arm/v7" --push
