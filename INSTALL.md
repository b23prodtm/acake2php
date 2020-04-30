# INSTALLATION
Docker :whale: is able to use web server containers in local (VM/Vbox) or remote (Cloud) machine cluster (Kubernetes).  
A typical install script could look like the following script, for instance edit a file :

		#!/usr/bin/env bash
		set -u

		cd myphpcms
		git pull
		git submodule update --init --recursive
		./deploy.sh x86_64 --docker

Docker builds up a new container and pushes it in registry.
It will eventually run the container as the startup script succeeds.

## Configuration to test localhost

		DB=Mysql ./configure.sh --docker --mig-database -u

## Circle CI
The current project is a full php with mariadb container for the Docker Virtual Machine (VM) manager, or Docker-CE, or even a ```Dockerfile``` compatible container interface. We choose Circle CI because it's able to achieve full remote tests with docker :whale: before we deploy to a Cloud Provider, Kubernetes Cluster, OpenShift, etc.

### Make local tests with CircleCI CLI
A local test may only run with a complete local Virtual Host (Vbox) configuration. See the requirements below.
Get started a Docker shell, and build through local Circle CLI:

		.circleci/build.sh

### [developers] Update the Docker deployment image
Rebuild image registry from deployment folder if you make change to the primary. E.g. change of Linux distribution. Edit the file deployment/images/primary/Dockerfile.template to your needs and perform a build from the a Docker client machine. If you make use of [Balena OS base image list](https://www.balena.io/docs/reference/base-images/base-images-ref/) repository you can use blocks to cross build for ARM ```# [ "cross-build-start" ] # [ "cross-build-end" ]``` command lines in the Dockerfile.template files. For instance, in a Terminal with Docker installed :

		./deployment/images/build.sh primary betothreeprod/raspberrypi3-php armhf

To deploy a Raspberry Pi with Docker or Balena Cloud.

		./deploy.sh armhf

### Requirements
- Broadband Internet access to the Worldwide Web, to download the packages and container images dependencies from the remote Docker registries.
- The [VBoxmanager](https://www.virtualbox.org/wiki/Downloads) package.
- The Docker VM described with the Dockerfile. >:whale: [Get Started](https://docs.docker.com/machine/get-started/) application.
- The CircleCI Client installed in ```$PATH```. [CLI Configuration](https://circleci.com/docs/2.0/local-cli/#section=configuration) shell command line :

		curl -fLSs https://circle.ci/cli | bash

Once everything is installed, please reboot your system.

### Startup
Everyting is ready to launch a container in real cluster environment. The process is described further. [Kubespray with Ansible and Kubernetes cluster](https://github.com/b23prodtm/kubesrpay).

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
