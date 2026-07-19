#!/usr/bin/env bash
set -eu

# Translate /run/secrets files into the variables expected by the upstream image before /init runs.
. /usr/local/bin/load-balena-secrets

exec /init "$@"
