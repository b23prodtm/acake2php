# INSTALLATION
Docker Hub is able to run web server containers in local or cloud machine cluster.  
A typical install script could look like the following _startup.sh_ 
Bourne Shell script :

	#!/usr/bin/env bash
	cd myphpcms
	git pull
	git submodule update --init --recursive
	./docker-compose-alias.sh -dns=domain.com -S -p=sqlrootpassword -t=testpassword -v $*
	cd ..

In bash run : '''startup.sh --build -d up''' You can add more docker-compose parameters as arguments.
Docker builds up a new container and pushes it in registry. 
It eventually runs the container as the startup script succeeds.

2018 - www.b23prodtm.info
-------------------------


