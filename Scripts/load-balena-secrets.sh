#!/usr/bin/env bash
set -eu

# Balena mounts runtime secrets in /run/secrets; local images also copy sample files there.
BALENA_SECRETS_DIR=${BALENA_SECRETS_DIR:-/run/secrets}

load_balena_secret() {
  [ "$#" -lt 1 ] && return 1
  env_name=$1
  file_name=${2:-$(printf '%s' "$env_name" | tr '[:upper:]' '[:lower:]')}
  current_value=$(printenv "$env_name" 2>/dev/null || true)
  secret_file="${BALENA_SECRETS_DIR}/${file_name}"

  if [ -n "$current_value" ] || [ ! -f "$secret_file" ]; then
    return 0
  fi

  secret_value=$(tr -d '\r' < "$secret_file")
  if [ -n "$secret_value" ]; then
    export "$env_name=$secret_value"
  fi
}

load_balena_secret MYSQL_ROOT_PASSWORD mysql_root_password
load_balena_secret MYSQL_PASSWORD mysql_password
load_balena_secret MYSQL_USER mysql_user
load_balena_secret MYSQL_DATABASE mysql_database
load_balena_secret MASTER_PASSWORD master_password
load_balena_secret MASTER_PASSWORD_HASH get_hash_password
load_balena_secret CAKEPHP_SECURITY_SALT security_salt
