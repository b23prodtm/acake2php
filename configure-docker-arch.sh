#!/usr/bin/env sh
usage="Usage $0 <arch>"
arch=$1
while [ true ]; do
  case $arch in
    1|arm32*|armv7l|armhf)
      arch="arm32v7"
      break;;
    2|arm64*|aarch64)
      arch="arm64v8"
      break;;
    3|amd64)
      arch="amd64"
      break;;
    *)
      echo $usage
      read -p "Set docker machine architecture ARM32, ARM64 bits or X86-64 (choose 1, 2 or 3) ? " arch
      ;;
  esac
done
ln -vsf ${arch}.env .env
eval $(cat ${arch}.env | grep DB_TAG)
sed -i.old -E -e /"mariadb"/s/"(mariadb:)[^:]+"/"\\1${DB_TAG}"/ docker-compose.yml
eval $(cat ${arch}.env | grep BALENA_MACHINE_NAME)
cd balena-sound/bluetooth-audio
sed -E -e s/"(%%BALENA_MACHINE_NAME%%)"/"${BALENA_MACHINE_NAME}"/g Dockerfile.template > Dockerfile.${BALENA_MACHINE_NAME}
cd ../..
