#!/bin/sh
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
        echo "Composer setup...\n"
        EXPECTED_SIGNATURE="$(curl -f https://composer.github.io/installer.sig)"
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        ACTUAL_SIGNATURE="$(php -r "echo hash_file('SHA384', 'composer-setup.php');")"

        if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
        then
            >&2 echo 'ERROR: Invalid installer signature'
            rm composer-setup.php
            exit 1
        fi

        php composer-setup.php --quiet --install-dir=bin
        rm composer-setup.php
else
        echo "Composer ${green}[OK]${nc}"
fi
echo `bin/composer.phar --version`
echo "\n
        If you see the message ${red}SHA1 signature could not be verified: broken signature${nc}\r
        Do ${cyan}rm bin/composer.phar${nc} please, and again ${cyan}sh ./Scripts/composer.sh${nc}.\r\n"
#; update plugins and dependencies
echo `bin/composer.phar update --with-dependencies`
echo `bin/composer.phar update -d app/Plugin/Markdown`
