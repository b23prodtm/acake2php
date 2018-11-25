#!/bin/bash
#;
#;
#; Composer simplifies the process to add features like plugins
#;
#;
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
composer="bin/composer.phar"
if [ ! -f $composer ]; then
        echo -e "Composer setup...\n"
        mkdir -p bin
        cd bin
        curl -sS https://getcomposer.org/installer | php
        cd ..
else
        echo -e "Composer ${green}[OK]${nc}"
fi
php bin/composer.phar --version
echo -e "\n
        //// FAQ:
        1. When ${orange}SHA1 signature could not be verified: broken signature${nc} appears on terminal :\r
            The composer binary wasn't downloaded on this machine or it must be updated :
            Please copy and run :
            ${cyan}rm bin/composer.phar && ./Scripts/composer.sh${nc}\n"
#; update plugins and dependencies
php bin/composer.phar update --with-dependencies --apcu-autoloader
