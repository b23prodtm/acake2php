#!/bin/sh
source ./Scripts/bootstrap.sh
if [ '${PHPCS}' != '1' ]; then
  ./lib/Cake/Console/cake test core AllTests --stderr;
else
  ./app/vendor/bin/phpcs -p --extensions=php --standard=CakePHP ./lib/Cake;
fi
