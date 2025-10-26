#!/usr/bin/env bash
# Update package list and install Python and required build tools
apk update && apk add --no-cache python3 build-base

# Download and install nvm:
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash

# shellcheck disable=SC1090
\. "$HOME/.nvm/nvm.sh"

# Download and install Node.js:
nvm install 18

# Verify the Node.js version:
node -v # Should print "v18.20.8".

# Download and install Yarn:
corepack enable yarn

# Verify Yarn version:
yarn -v

