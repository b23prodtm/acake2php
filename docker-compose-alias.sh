#!/usr/bin/env bash
# shellcheck source=Scripts/lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
bootargs=""
docker=""
saved=("$*")
usage="[-dns=<domainname>] [-e,--export=<value>] [-o bootstrap arguments]"
[ $# -eq 0 ] && echo "Usage: $0 ${usage}" && exit 1
while [[ "$#" -gt 0 ]]; do case $1 in
    -[eE]*|--export*)
      parse_arg_export"$1" "ARG_EXPORT" "argument ARG_EXPORT=\$ARG_EXPORT was exported";;
    -[vV]*|--verbose )
      echo "Passed params :  $0 ${saved[*]}";;
    -[oO]*)
      bootargs=("${saved[*]}");;
    -dns*|-DNS*)
      parse_dns_host "$1" "SERVER_NAME" "Domain Server Name";;
    -[hH]*|--help )
      echo "Usage: $0 ${usage}
        -dns=<domainname>
	    Apache ServerName global directive
	-e, --expôrt=<value>
            Exports ARG_EXPORT
        -v
            Verbosity enabled
	[other-args]
	    Passed to docker-compose
        "
        exit 0;;
    *) docker="${docker} $1";;
esac; shift; done
export DB=Mysql
source Scripts/bootstrap.sh "${bootargs[*]}"
if [ ! "$(command -v docker-compose 2> /dev/null)" ]; then Scripts/install-docker-compose.sh; fi
if [ -n "$SERVER_NAME" ]; then
    source Scripts/docker_site_conf.sh "$SERVER_NAME"
else
    cp -v docker/apache/site-default.conf docker/apache/site.conf
fi
docker-compose "$docker"
sudo cp index-redirect-8000.php /var/www/html/index.php
