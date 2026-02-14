#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
balena_deploy . armhf 3 0
docker buildx bake -f docker-bake.hcl php-fpm \
  --set "*.platform=linux/arm/v7" \s
  --secret id=mysql_user,env=MYSQL_USER \
  --secret id=mysql_password,env=MYSQL_PASSWORD \
  --secret id=mysql_root_password,env=MYSQL_ROOT_PASSWORD \
  --secret id=mysql_database,env=MYSQL_DATABASE \
  --secret id=master_password,env=MASTER_PASSWORD \
--push
