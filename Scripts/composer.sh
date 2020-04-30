#!/usr/bin/env bash
#;
#;
#; Composer simplifies the process to add features like plugins
#;
#;
#; colorful shell
source ./Scripts/lib/logging.sh
source ./Scripts/lib/parsing.sh
composer="bin/composer.phar"
if [ $(which composer) 2> /dev/null ]; then
        composer="composer"
elif [ ! -f $composer ]; then
        slogger -st $0 "Composer setup...\n"
        mkdir -p bin
        cd bin
        curl -sS https://getcomposer.org/installer | php
        cd ..
fi
slogger -st $0 "Composer ${green}[OK]${nc}"
bash -c "${composer} --version"
#; update plugins and dependencies (composer install is good enough to check for updates)
bash -c "${composer} $*"
