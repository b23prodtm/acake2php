#!/usr/bin/env bash
set -eu
TOPDIR="$(cd "$(dirname \
"$(dirname "${BASH_SOURCE[0]}")")" && pwd)"
ln -sf "${TOPDIR}/aarch64.env" "${TOPDIR}/.env"
balena_deploy "${TOPDIR}" aarch64 3 0
ln -sf "${TOPDIR}/docker-compose.aarch64" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f "${TOPDIR}/docker-compose.yml" -f "${TOPDIR}/docker-bake.hcl" balena-storage \
  --set "*.platform=linux/arm64" --push
