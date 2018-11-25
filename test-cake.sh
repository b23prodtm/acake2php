#!/bin/bash
while [[ "$#" > 0 ]]; do case $1 in
  -[tT]*|--travis )
    #; Test values
    export DB="Mysql"
    export COLLECT_COVERAGE="false"
    export TRAVIS_OS_NAME="osx"
    export TRAVIS_PHP_VERSION=$(php -v | grep -E "[5-7]\.\\d+\.\\d+" | cut -d " " -f 2 | cut -c 1-3
    )
    source .travis/configure;;
  --cov )
    export COVERITY_SCAN_BRANCH=1;;
  -[hH]*|--help )
    echo "./test-cake.sh [-t, --travis [--cov]]
      -t Travis CI Test Workflow
      --cov Coverity Scan tests"
      exit 0;;
  * ) ;;
esac; shift; done
source ./Scripts/bootstrap.sh
if [ "${COVERITY_SCAN_BRANCH}" != 1 ]; then
  if [ '${PHPCS}' != '1' ]; then
    ./lib/Cake/Console/cake test core AllTests --stderr
  else
    app/vendor/bin/phpcs -p --extensions=php --standard=CakePHP ./lib/Cake
  fi
else
  php app/vendor/bin/phpunit --coverage-clover build/logs/clover.xml --stop-on-failure -c app/phpunit.xml.dist app/Test/Case/AllTestsTest.php
fi
