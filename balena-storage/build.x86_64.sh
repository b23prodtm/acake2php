#!/usr/bin/env bash
set -eu
TOPDIR="$(cd "$(dirname \
"$(dirname "${BASH_SOURCE[0]}")")" && pwd)"
ln -sf "${TOPDIR}/x86_64.env" "${TOPDIR}/.env"
balena_deploy "${TOPDIR}" x86_64 3 0
ln -sf "${TOPDIR}/docker-compose.x86_64" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f "${TOPDIR}/docker-compose.yml" -f "${TOPDIR}/docker-bake.hcl" balena-storage \
  --set "*.platform=linux/amd64" --push
