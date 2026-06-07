## Build the Docker VM images
Docker :whale: VM or a remote fleet Balena/Kubernetes/etc.  
A typical install script could look like the following script .circleci/config.yml
It will eventually run the container as the startup script succeeds.

## Quick VM Startup
Everyting is ready to launch a container in real cluster environment. The process is described further.
We have provided 3 ways to make use of this project. It supports:
	- [Docker CE](https://docs.docker.com/machine/get-started/)
		`docker-compose up` may be enough to run and test the configuration
	- [Balena Cloud](https://www.balena.io/docs/learn/getting-started/raspberrypi3/nodejs/)
		`./deploy.sh armhf --balena`
	- [Kubernetes](https://kubernetes.io//docs/concepts/overview/what-is-kubernetes/)
		`./kompose.sh up` may be run if your shell run on a valid working cluster environment

Please read README.md file to get more information on how to setup the cluster and handle common issues.

## Requirements
- Broadband Internet access to the Worldwide Web, to download the packages and container images dependencies from the remote Docker registries.
- The Docker CE described with the Dockerfile. >:whale: [Get Started](https://docs.docker.com/machine/get-started/) application.
- NodeJS command line package manager interface, [npmjs](https://www.npmjs.com/get-npm) with yarn package manager
- A BASH Terminal (Linux or Darwin OS are known to work)

Once everything is installed, please reboot your system.

## Webserver configuration (Source balena.yml)
Very few variables are defined by default. It provides host-container-server communication. Host Firewall and file attributes set to the host platform values.
  
  		# Open https://${SERVER_NAME}/etc/getHashPassword.php or type $ ./configure.sh -p password -s hash
  		# Get new staff credentials (url=/admin/index.php)
                - MASTER_PASSWORD_HASH: (let's encrypt it from above)
  
		# Database name
  		- MYSQL_DATABASE: aria_db
		# Persistent ROOT connection credentials
		- MYSQL_USER: maria
                - MYSQL_USER_PASSWORD: Some-robust-Password
		- MYSQL_ROOT_PASSWORD: SoMe-MorE-Robust-PAssWOrd!

## Some configuration. All variables may be changed to your needs:
      
		# CakePHP secrets
		- CAKEPHP_SECRET_TOKEN:<secret-token>
		- CAKEPHP_SECRET_SALT:<secret-salt>
		- CAKEPHP_SECURITY_CIPHER_SEED:<cipher-seed>
  		
    		# Deployed Migration option
    		- MIGRATE_OPTION: -v
		
  		# The following values are options to change if needed
		# Binding a mysql container to a specific (public) IP address or all (0.0.0.0)
		- MYSQL_BIND_ADDRESS: 0.0.0.0
		- MYSQL_TCP_PORT: 3306

		# Persistent USER connection credentials
		- MYSQL_USER: maria
		- MYSQL_PASSWORD: maria-abc
  
		# Run as a different user-group space ($ id -g $USER)
		- PGID: 0
		# Run as a different user space ($ id -u $USER)
		- PUID: 0

    		# Apache 2 httpd, or DNS CNAME of the host machine ($ hostname)
		- SERVER_NAME: www-machine.local
  
		# MariaDB Timezone
		- TZ: Europe/Paris
  
## Validate the configuration, and eventually test it:
Requirements: A Docker or any compatible must be installed and running.
Argument value `--docker` set up a local docker test configuration.

		./configure.sh --docker --mig-database -u -i
                ./test-cake.sh --docker

## Circle CI
Developer build continuous integration
The current project is a full PHP (CakePHP) with MySQL (MariaDB) container for Docker-CE, or even a ```Dockerfile``` compatible container interface. We choose Circle CI because it's able to achieve full remote tests with docker :whale: before we deploy to a devices swarm. It actually can run on self hosted runners and remote runnners from .circle/config.yml configuration file presets.

### [developers] Update the Docker deployment image
A. Fork the master repository (development branch).
    
    yarn

Run the tools directly from .bin:

    ./node_modules/.bin/balena_deploy --help

Or add the local bin to your PATH temporarily:

    export PATH="./node_modules/.bin:$PATH"


B. Rebuild image registry from deployment folder if you make change to the primary. E.g. change of Linux distribution. Edit the file deployment/images/primary/Dockerfile.template to your needs and perform a build from the a Docker client machine.
If you make use of [Balena OS base image list](https://www.balena.io/docs/reference/base-images/base-images-ref/) repository you can use blocks to cross build for ARM ```# [ "cross-build-start" ] # [ "cross-build-end" ]``` command lines in the Dockerfile.template files:

    ./deploy.sh aarch64 --local --build-deps

C. With the BalenaCloud and BalenaHub service, you may see the message on build logs:
[Error]               Error: The command 'cross-build-start' returned a non-zero code: 1:
Run a push, even if it's unsuccessful:

    ./deploy.sh aarch64 --balena --push

You have now disabled the cross-build environment.
Browse up to your github master fork-repository.
Click on Deploy with Balena in the README, choose a fleet and deploy it!

### License
   Copyright 2016-2025 www.b23prodtm.info

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   * [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
