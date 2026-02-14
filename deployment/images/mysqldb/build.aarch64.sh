#!/usr/bin/env bash
set -eu
TOPDIR="$(cd "$(dirname "$(dirname "$(dirname \
"$(dirname "${BASH_SOURCE[0]}")")")")" && pwd)"
ln -s "${TOPDIR}/aarch64.env" aarch64.env
cp -vf "${TOPDIR}/docker-compose.${BALENA_ARCH}" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f "${TOPDIR}/docker-bake.hcl" db \
  --set "*.platform=linux/arm64" \
  --push
