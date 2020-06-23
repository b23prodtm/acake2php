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
        slogger -st $0 "Composer setup...\n"
        mkdir -p bin
        cd bin || exit 1
        curl -sS https://getcomposer.org/installer | php
        cd ..
fi
# shellcheck disable=SC2154
slogger -st $0 "Composer ${green}[OK]${nc}"
bash -c "${composer} --version"
#; update plugins and dependencies (composer install is good enough to check for updates)
bash -c "${composer} $*"
