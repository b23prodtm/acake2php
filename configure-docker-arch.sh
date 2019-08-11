#!/usr/bin/env sh
usage="Usage $0 <arch>"
arch=$1
while [ true ]; do
  case $arch in
    1|arm32*|armv7l|armhf) 
      arch="armv7l"
      break;;
    2|arm64*|aarch64)
      arch="aarch64"
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

