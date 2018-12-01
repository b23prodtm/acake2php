#!/bin/bash
source ./Scripts/lib/parsing.sh
bootargs=""
while [[ "$#" > 0 ]]; do case $1 in
  -[tT]*|--travis )
    #; Test values
    export DB="Mysql"
    export COLLECT_COVERAGE="false"
    export TRAVIS_OS_NAME="osx"
    export TRAVIS_PHP_VERSION=$(php -v | grep -E "[5-7]\.\\d+\.\\d+" | cut -d " " -f 2 | cut -c 1-3
    )
    # remote servers CI doesn't need a root password import (-i) but the socket => -y
    source configure.sh "-c" "-h" "-p" "pass" "-s" "word" "--mig-database" "-i"
    source .travis/configure.sh;;
  --cov )
    export COLLECT_COVERAGE=true;;
  -[hH]*|--help )
    echo "Usage: ./test-cake.sh [-p|--sql-password=<password>][--test-sql-password=<password>] [-t, --travis [--cov]]
      -p, --sql-password=<password>
          Exports DATABASE_PASSWORD to bootargs.
      --test-sql-password=<password>
          Exports TEST_DATABASE_PASSWORD
      -t
          Travis CI Local Test Workflow
      --cov
          Coverage All Tests
      -o, --openshift
          Use environment variables from real pod or current shell
      "
      exit 0;;
  -[pP]*|--sql-password*)
    export DATABASE_PASSWORD=$(parse_sql_password "$1" "$DATABASE_USER");;
  --test-sql-password*)
    export TEST_DATABASE_PASSWORD=$(parse_sql_password "$1" "$TEST_DATABASE_USER");;
  -[vV]*|--verbose )
    bootargs="${bootargs} ${1}";;
  -[oO]*|--openshift )
    bootargs="${bootargs} --real";;
  *) echo "Unknown parameter passed: $1"; exit 1;;
esac; shift; done
source ./Scripts/bootstrap.sh $bootargs
if [[ "$COLLECT_COVERAGE" == "true" ]]; then
  ./app/Vendor/bin/phpunit --coverage-clover app/build/logs/clover.xml --stop-on-failure -c app/phpunit.xml.dist app/Test/Case/AllTestsTest.php
else
  if [ '${PHPCS}' != '1' ]; then
    ./lib/Cake/Console/cake test core AllTests --stderr
  else
    ./app/Vendor/bin/phpcs -p --extensions=php --standard=CakePHP ./lib/Cake
  fi
fi
