#!/usr/bin/env bash
export  _PHP=7.1.27
export _PKG=php
LINUX=$(awk -F= '/^NAME/{print $2}' /etc/os-release | grep -o "\w*"| head -n 1)
_OS=linux
if [[ -z PHPENV_ROOT ]]; then
	export PHPENV_ROOT=~/.phpenv
	sudo bash .travis/TravisCI-OSX-PHP/build/phpenv_install.sh
	sudo .travis/TravisCI-OSX-PHP/build/prepare_${_OS}_env.sh
fi
#; phpenv install -i .travis/TravisCI-OSX-PHP/build/.travis_${_OS}.php.ini $_PHP
rm -rf $PHPENV_ROOT/shims/composer
case $LINUX in
	'Ubuntu'|"Debian") 
		export PHP_BUILD_CONFIGURE_OPTS=--with-openssl
		sudo apt install libxml2 php7.2-cli php7.2-xml;;
	*) 
		echo "Invalid LINUX: ${LINUX}"
		exit 1;;
esac
ADDITIONAL_PHP_INI=.travis/TravisCI-OSX-PHP/build/.travis_${_OS}.php.ini .travis/TravisCI-OSX-PHP/build/custom_php_ini.sh 
curl -s http://getcomposer.org/installer | php && php composer.phar install --dev --no-interaction
./Scripts/docker-compose-alias.sh --domain=b23prodtm.info -S -p=sqlrootpassword -t=testpassword -v up -d --build
