#!/usr/bin/env bash
#;
#;
#; Composer simplifies the process to add features like plugins
#;
#;
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
composer="bin/composer"
if [ -n "$(command -v composer)" ]; then
        composer="composer"
elif [ ! -f $composer ]; then
        slogger -st "$0" "Composer setup...\n"
	mkdir -p "$(dirname "$composer")"
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === 'ed0feb545ba87161262f2d45a633e34f591ebb3381f2e0063c345ebea4d228dd0043083717770234ec00c5a9f9593792') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
  php composer-setup.php
	php -r "unlink('composer-setup.php');"
	mv -v composer.phar "$composer"
fi
# shellcheck disable=SC2154
slogger -st "$0" "Composer ${green}[OK]${nc}"
bash -c "${composer} --version"
#; update plugins and dependencies (composer install is good enough to check for updates)
bash -c "${composer} $*"
