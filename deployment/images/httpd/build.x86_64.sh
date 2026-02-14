#!/usr/bin/env bash
set -eu
TOPDIR="$(cd "$(dirname "$(dirname "$(dirname \
"$(dirname "${BASH_SOURCE[0]}")")")")" && pwd)"
ln -s "${TOPDIR}/x86_64.env" x86_64.env
cp -vf "${TOPDIR}/docker-compose.${BALENA_ARCH}" "${TOPDIR}/docker-compose.yml"
docker buildx bake -f "${TOPDIR}/docker-bake.hcl" httpd \
  --set "*.platform=linux/amd64" \
  --push
