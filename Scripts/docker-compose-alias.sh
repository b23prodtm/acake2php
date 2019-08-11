#!/usr/bin/env bash
source ./Scripts/lib/parsing.sh
bootargs=""
docker=""
saved=("$*")
usage="[--domain=<domainname>] [-p|--sql-password=<password>] [-t,--test-sql-password=<password>] [other-args]
$0 --dommain=example.com -p=foo -t=bar -v up -d --build
$0 --openshift --dommain=realdomain.com -v up -d --build

Builds up a container for the Docker Hub.
If an 'exec format error' occurs, run the script ./configure-docker-arch.sh for your Docker machine.
Enter $0 --help for more about this script"
[ $# -eq 0 ] && echo "Usage: $0 ${usage}" && exit 1
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
    --domain*)
      parse_dom_host "$1" "SERVER_NAME" "Domain Server Name";;
    -[hH]*|--help )
      echo "Usage: $0 ${usage}
        --domain=<domainname>
	    Apache ServerName global directive
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
logger -st docker-compose "Evaluating file .env environment variables..."
eval $(cat .env)
source Scripts/bootstrap.sh $bootargs
if [ ! $(which docker-compose) 2> /dev/null ]; then Scripts/install-docker-compose.sh; fi
[ -z $SERVER_NAME ] && SERVER_NAME=local
source Scripts/configure-available-site.sh $SERVER_NAME
docker-compose $docker
