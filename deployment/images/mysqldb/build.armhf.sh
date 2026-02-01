#!/usr/bin/env bash
set -eu
TOPDIR="$(cd "$(dirname "$(dirname "$(dirname "$(dirname "${BASH_SOURCE[0]}")")")")" && pwd)"
ln -s "${TOPDIR}/armhf.env" armhf.env
balena_deploy "${TOPDIR}" armhf 3 0
docker buildx bake -f "${TOPDIR}/docker-bake.hcl" db \
  --set "*.platform=linux/arm/v7" --push
