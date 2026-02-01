#!/usr/bin/env bash
set -eu
TOPDIR="$(cd "$(dirname "$(dirname "$(dirname "$(dirname "${BASH_SOURCE[0]}")")")")" && pwd)"
ln -s "${TOPDIR}/aarch64.env" aarch64.env
balena_deploy "${TOPDIR}" aarch64 3 0
docker buildx bake -f "${TOPDIR}/docker-bake.hcl" db \
  --set "*.platform=linux/arm64" --push
