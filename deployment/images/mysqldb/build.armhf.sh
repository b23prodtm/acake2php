#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname $(dirname $(dirname $(dirname "${BASH_SOURCE[0]}"))))" && pwd)
balena_deploy . armhf 3 0
docker buildx bake -f docker-bake.hcl --service mysqldb \
  --set "*.platform=linux/arm/v7"
