#!/bin/sh
#;
#;
#; Composer simplifies the process to add features like plugins
#;
#;
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
#; update plugins and dependencies
echo `bin/composer.phar update --with-dependencies`
echo `bin/composer.phar update -d app/Plugin/Markdown`
