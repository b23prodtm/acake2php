# Download and install nvm:
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash

# in lieu of restarting the shell
\. "~/.nvm/nvm.sh"

# Download and install Node.js:
nvm install 18

# Verify the Node.js version:
node -v # Should print "v18.20.8".

# Download and install Yarn:
corepack enable yarn

# Verify Yarn version:
yarn -v

