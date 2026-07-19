#!/usr/bin/env bash
# manifest-push.sh — Create and push multi-platform Docker manifests.
# Reads DOCKER_ORG and BALENA_PROJECTS from common.env (or environment).
# Call this after all per-arch images have been built and pushed.
#
# Usage:
#   BAKE_TAG=main DOCKER_ORG=betothreeprod ./manifest-push.sh
set -eu

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Load project defaults (DOCKER_ORG, BALENA_PROJECTS, etc.)
# shellcheck source=common.env
if [ -f "${SCRIPT_DIR}/common.env" ]; then
  # shellcheck disable=SC1091
  set -a; . "${SCRIPT_DIR}/common.env"; set +a
fi

DOCKER_ORG="${DOCKER_ORG:-betothreeprod}"
BAKE_TAG="${BAKE_TAG:-latest}"
ARCHES=("x86_64" "aarch64" "armhf")

# The service image names built by docker-bake.hcl
IMAGES=("mysqldb" "php-fpm" "httpd" "balena-storage")

arch_tag() {
  local arch="$1"
  # Arch-suffixed tag built by the CI matrix
  echo "${BAKE_TAG}-${arch}"
}

for img in "${IMAGES[@]}"; do
  echo "==> Creating manifest for ${DOCKER_ORG}/${img}:${BAKE_TAG}"

  SOURCES=()
  for arch in "${ARCHES[@]}"; do
    SOURCES+=("${DOCKER_ORG}/${img}:$(arch_tag "$arch")")
  done

  # Remove existing local manifest (ignore errors)
  docker manifest rm "${DOCKER_ORG}/${img}:${BAKE_TAG}" 2>/dev/null || true

  docker manifest create "${DOCKER_ORG}/${img}:${BAKE_TAG}" "${SOURCES[@]}"
  docker manifest push   "${DOCKER_ORG}/${img}:${BAKE_TAG}"

  # Also keep :latest in sync when pushing a named tag
  if [ "${BAKE_TAG}" != "latest" ]; then
    echo "==> Updating manifest ${DOCKER_ORG}/${img}:latest"
    docker manifest rm "${DOCKER_ORG}/${img}:latest" 2>/dev/null || true
    docker manifest create "${DOCKER_ORG}/${img}:latest" "${SOURCES[@]}"
    docker manifest push   "${DOCKER_ORG}/${img}:latest"
  fi
done

echo "All multi-platform manifests pushed successfully."
