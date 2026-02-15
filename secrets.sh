#!/bin/bash

set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)

# Define the directory containing the secrets
SECRETS_DIR="${TOPDIR}/.balena/secrets"

# Array of secret files
declare -a secrets=(
    "secret_mysql_root_password"
    "secret_mysql_user"
    "secret_mysql_password"
    "secret_mysql_database"
    "secret_master_password"
)

# Function to display current secrets
display_secrets() {
    echo "Current secrets:"
    for secret in "${secrets[@]}"; do
        echo "$secret: $(cat "$SECRETS_DIR/$secret")"
    done
    echo
}

# Function to edit a specific secret file
edit_secret() {
    local secret_file="$1"
    echo "Editing $secret_file..."
    nano "$secret_file"  # You can replace 'nano' with your preferred text editor
}

# Check if the secrets directory exists
if [[ ! -d "$SECRETS_DIR" ]]; then
    echo "Secrets directory not found!"
    exit 1
fi

# Display current secrets
display_secrets

# Prompt user to edit each secret file
for secret in "${secrets[@]}"; do
    read -r -p "Do you want to edit the secret file for $secret? (y/n): " choice
    if [[ "$choice" == "y" || "$choice" == "Y" ]]; then
        edit_secret "$SECRETS_DIR/$secret"
        echo "$secret file updated."
    else
        echo "No changes made to $secret file."
    fi
done

