#!/usr/bin/env bash
set -eu

# Keep runtime secret values available for CakePHP before php-fpm starts serving requests.
. /usr/local/bin/load-balena-secrets

exec "$@"
