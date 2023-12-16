## Build the virtual machine (VM)
Docker :whale: is able to use web server containers in local VM or a remote cluster.  
A typical install script could look like the following script, for instance edit a file :

		#!/usr/bin/env bash
		set -u
		export PATH="node_modules/.bin:$PATH"
		cd acake2php
		git clone https://github.com/b23prodtm/acake2php.git
		git submodule sync && git submodule update --init --recursive
		npm install --omit=optional
		# reset architecture flags
		./deploy.sh x86_64 --nobuild 0
		./deploy.sh x86_64 --build-deps --docker

Docker builds up a new container and pushes it in registry.
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

## VM Requirements
- Broadband Internet access to the Worldwide Web, to download the packages and container images dependencies from the remote Docker registries.
- The Docker CE described with the Dockerfile. >:whale: [Get Started](https://docs.docker.com/machine/get-started/) application.
- NodeJS command line package manager interface, [npmjs](https://www.npmjs.com/get-npm)
- A BASH Terminal (Linux or Darwin OS are known to work)
- A virtualization system like [VBoxmanager](https://www.virtualbox.org/wiki/Downloads) must be installed for your OS.

Once everything is installed, please reboot your system.

## Webserver configuration
A few variables are defined in containers environment provides client-server communication.

		# Persistent ROOT connection credentials
		MYSQL_HOST: localhost
		DATABASE_USER: root
		MYSQL_ROOT_PASSWORD: mariadb
		# CakePHP secrets
		CAKEPHP_SECRET_TOKEN:<secret-token>
		CAKEPHP_SECRET_SALT:<secret-salt>
		CAKEPHP_SECURITY_CIPHER_SEED:<cipher-seed>

Some configuration changes may broke the installation with Docker and on file permissions. The following default variables may be setup as your server preferences, set in open source:

		# The following values are options to change if needed
		# Binding a mysql container to a specific (public) IP address or all (0.0.0.0)
		MYSQL_BIND_ADDRESS=0.0.0.0

		# Run as a different user space ($ id -u $USER)
		PUID=0
		# Run as a different user-group space ($ id -g $USER)
		PGID=0

		# MariaDB Timezone
		TZ=Europe/Paris

		# Persistent USER connection credentials
		MYSQL_USER=maria
		MYSQL_PASSWORD=maria-abc

		# staff credentials (url=/admin/index.php) given $ ./configure.sh -h -p pass -s salt
		GET_HASH_PASSWORD=<HaSheD-PasSwoRd>

    # Apache 2 httpd, or DNS CNAME of the host machine ($ hostname)
		SERVER_NAME=www-machine.local

## Validate the configuration

		./configure.sh --docker --mig-database -u -i

		This should pass until it updates the database. This can succeed only if the [Webserver](#Webserver-configuration) initialization did well with your settings. The webserver must be ready to use.

## Circle CI
The current project is a full php with mariadb container for the Docker Virtual Machine (VM) manager, or Docker-CE, or even a ```Dockerfile``` compatible container interface. We choose Circle CI because it's able to achieve full remote tests with docker :whale: before we deploy to a Cloud Provider, Kubernetes Cluster, OpenShift, etc.

### Make local tests with CircleCI CLI
A local test may only run with a complete local Virtual Host (Vbox) configuration. See the requirements below.
Get started a Docker shell, and build through local Circle CLI:

		.circleci/build.sh

* **circleci cli**

- The CircleCI Client installed in ```$PATH```. [CLI Configuration](https://circleci.com/docs/2.0/local-cli/#section=configuration) shell command line :

				curl -fLSs https://circle.ci/cli | bash


### [developers] Update the Docker deployment image
Rebuild image registry from deployment folder if you make change to the primary. E.g. change of Linux distribution. Edit the file deployment/images/primary/Dockerfile.template to your needs and perform a build from the a Docker client machine. If you make use of [Balena OS base image list](https://www.balena.io/docs/reference/base-images/base-images-ref/) repository you can use blocks to cross build for ARM ```# [ "cross-build-start" ] # [ "cross-build-end" ]``` command lines in the Dockerfile.template files. For instance, in a Terminal with Docker installed, at first dependencies may be built :

    ./deploy.sh armhf --nobuild --build-deps

To deploy a Raspberry Pi with Docker or Balena Cloud.

		./deploy.sh armhf --balena

### License
   Copyright 2016 www.b23prodtm.info

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   * [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
