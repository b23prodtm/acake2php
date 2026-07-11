#!/usr/bin/env bash
set -eu

TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)

# ============================================================================
# INITIALISATION - Read from docker-bake.hcl (single source of truth)
# ============================================================================

# Verify docker-bake.hcl
if [[ ! -f "${TOPDIR}/docker-bake.hcl" ]]; then
    echo "❌ Error: docker-bake.hcl not found at ${TOPDIR}/docker-bake.hcl"
    exit 1
fi

# Parse REGISTRY from docker-bake.hcl
REGISTRY=${REGISTRY:-$(grep -A1 'variable "REGISTRY"' "${TOPDIR}/docker-bake.hcl" | grep 'default' | sed 's/.*default = "\(.*\)".*/\1/')}

# Parse REGISTRY_IMAGE (DOCKER_ORG) from docker-bake.hcl
REGISTRY_IMAGE=${REGISTRY_IMAGE:-$(grep -A1 'variable "DOCKER_ORG"' "${TOPDIR}/docker-bake.hcl" | grep 'default' | sed 's/.*default = "\(.*\)".*/\1/')}

# Verify common.env for BALENA_PROJECTS
if [[ ! -f "${TOPDIR}/common.env" ]]; then
    echo "❌ Error: common.env not found (required for BALENA_PROJECTS)"
    exit 1
fi

# shellcheck source=common.env
source "${TOPDIR}/common.env"

# ============================================================================
# GIT VARIABLES - with robust fallback logic
# ============================================================================

BAKE_TAG="${BAKE_TAG:-$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo 'unknown')}"
GITHUB_SHA="${GITHUB_SHA:-$(git rev-parse --short=7 HEAD 2>/dev/null || echo 'unknown')}"

# ============================================================================
# VALIDATIONS
# ============================================================================

: "${REGISTRY?Error: REGISTRY not defined}"
: "${REGISTRY_IMAGE?Error: REGISTRY_IMAGE not defined}"

if [[ "${BAKE_TAG}" == "unknown" || "${GITHUB_SHA}" == "unknown" ]]; then
    echo "⚠️  Warning: Git info missing (detached repo or git absent)"
    echo "   BAKE_TAG=${BAKE_TAG}, GITHUB_SHA=${GITHUB_SHA:0:7}"
fi

# ============================================================================
# MANIFEST MERGE - Create & push manifests for ALL services
# ============================================================================

echo ""
echo "🔄 Creating and pushing multi-platform manifests for ALL services"
echo "   REGISTRY=${REGISTRY}"
echo "   IMAGE=${REGISTRY_IMAGE}"
echo "   BAKE_TAG=${BAKE_TAG}"
echo ""

for SERVICE in "${BALENA_PROJECTS[@]}"; do
    IMAGE_BASE="${REGISTRY}/${REGISTRY_IMAGE}/${SERVICE}"

    echo "📦 Processing: ${SERVICE}"
    docker buildx imagetools create -t \
      "${IMAGE_BASE}:${GITHUB_SHA}" \
      "${IMAGE_BASE}:${GITHUB_SHA}-amd64" \
      "${IMAGE_BASE}:${GITHUB_SHA}-arm32v7" \
      "${IMAGE_BASE}:${GITHUB_SHA}-arm64v8"

    # Also push as latest if on main or development branch
    if [[ "${BAKE_TAG}" == "main" || "${BAKE_TAG}" == "development" ]]; then
        echo "  Creating latest manifest..."
        docker buildx imagetools create -t \
          "${IMAGE_BASE}:latest" \
          "${IMAGE_BASE}:${GITHUB_SHA}-amd64" \
          "${IMAGE_BASE}:${GITHUB_SHA}-arm32v7" \
          "${IMAGE_BASE}:${GITHUB_SHA}-arm64v8"
    fi

    echo "  ✅ ${SERVICE} done"
    echo ""
done

echo "✅ All manifests pushed successfully"
