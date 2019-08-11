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
    *)
      echo $usage
      read -p "Set docker machine architecture ARM 32 bits or 64 bits (choose 1 or 2) ? " arch   
      ;;
  esac
done

ln -vsf ${arch}.env .env

