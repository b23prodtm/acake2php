#!/usr/bin/env bash
source ./Scripts/lib/parsing.sh
bootargs=""
docker=""
saved=("$*")
while [[ "$#" > 0 ]]; do case $1 in
    -[pP]*|--sql-password*)
      parse_sql_password "$1" "DATABASE_PASSWORD" "user ${DATABASE_USER}";;
    -[tT]*|--test-sql-password*)
      parse_sql_password "$1" "TEST_DATABASE_PASSWORD" "test user ${TEST_DATABASE_USER}";;
    -[vV]*|--verbose )
      echo "Passed params :  $0 ${saved}";;
    -[oO]*|--openshift )
      bootargs=$saved;;
    -[S]*|-submodule )
      git submodule update --init --recursive;;
    -[hH]*|--help )
      echo "Usage: $0 [-p|--sql-password=<password>] [-t,--test-sql-password=<password>] [other-args]
        -p, --sql-password=<password>
            Exports DATABASE_PASSWORD
        -t, --test-sql-password=<password>
            Exports TEST_DATABASE_PASSWORD
        -S, --submodule
            Update Git submodules
        -o, --openshift
            Use environment variables from real pod or current shell. Also calls composer update (heavy load task).
        -v
            Verbosity enabled
	[other-args]
	    Passed to docker-compose
        "
        exit 0;;
    *) docker="${docker} $1";;
esac; shift; done
export DB=Mysql
export _PHP=7.0
export _PKG=php
export PHPENV_ROOT=~/.phpenv
source .travis/TravisCI-OSX-PHP/build/phpenv_install.sh
source .travis/TravisCI-OSX-PHP/build/prepare_linux_env.sh
source ./Scripts/bootstrap.sh $bootargs
if [[ ! $(which docker-compose) > /dev/null ]]; then Scripts/install-docker-compose.sh; fi
docker-compose $docker
