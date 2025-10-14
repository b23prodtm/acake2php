#!/usr/bin/env bash
ARC=(x86_64 aarch64 armhf)
# Loop through the array and echo each value
for arch in "${ARC[@]}"; do
  printf "Updating templates, %s \n" "$arch"
  ./deploy.sh "$arch" 3 0 2> /dev/null > /dev/null
done
