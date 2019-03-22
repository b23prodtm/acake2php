#!/usr/bin/env bash
export  _PHP=7.1.27
export _PKG=php 
if [[ -z PHPENV_ROOT ]]; then
	export PHPENV_ROOT=~/.phpenv 
	sudo bash .travis/TravisCI-OSX-PHP/build/phpenv_install.sh
	sudo .travis/TravisCI-OSX-PHP/build/prepare_linux_env.sh 
fi
export PHP_BUILD_CONFIGURE_OPTS=--with-openssl
#; phpenv install -i .travis/TravisCI-OSX-PHP/build/.travis_linux.php.ini $_PHP
rm -rf $PHPENV_ROOT/shims/composer 
sudo apt install libxml2 php7.2-cli php7.2-xml
ADDITIONAL_PHP_INI=.travis/TravisCI-OSX-PHP/build/.travis_linux.php.ini .travis/TravisCI-OSX-PHP/build/custom_php_ini.sh 
curl -s http://getcomposer.org/installer | php && php composer.phar install --dev --no-interaction
./docker-compose-alias.sh -dns=domain.com -S -p=sqlrootpassword -t=testpassword -v up --build -d
