#!/usr/bin/env bash
ARC=(armhf x86_64 aarch64)
# Fixes: not inside a working tree
REV=$(git -C .git rev-parse 2>/dev/null)
# Loop through the array and echo each value
for arch in "${ARC[@]}"; do
  printf "Updating templates, %s \n" "$arch"
  ./deploy.sh "$arch" 1 0 0 2> /dev/null > /dev/null
done
[[ "$REV" -eq 0 ]] && git add docker-compose.yml
[[ "$REV" -eq 0 ]] && git commit -m "Updated Templates"
