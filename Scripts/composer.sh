#!/usr/bin/env bash
#;
#;
#; Composer simplifies the process to add features like plugins
#;
#;
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
composer="bin/composer.phar"
if [ -n "$(command -v composer)" ]; then
        composer="composer"
elif [ ! -f $composer ]; then
        slogger -st "$0" "Composer setup...\n"
	mkdir -p "$(dirname "$composer")"
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	mv -v composer.phar "$composer"
fi
# shellcheck disable=SC2154
slogger -st "$0" "Composer ${green}[OK]${nc}"
bash -c "${composer} --version"
#; update plugins and dependencies (composer install is good enough to check for updates)
bash -c "${composer} $*"
