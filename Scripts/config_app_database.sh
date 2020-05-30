#!/usr/bin/env bash
sqlversion="5.7"
source ./Scripts/lib/logging.sh
source ./Scripts/lib/parsing.sh
docker=$(parse_arg_exists "--docker" $*)
MARIADB_SHORT_NAME=$(echo $SECONDARY_HUB | awk -F/ '{ print $2 }' | awk -F: '{ print $1 }')
if [ $docker 2> /dev/null ]; then
	./Scripts/start_daemon.sh ${docker}
else
	if [ ! $(which brew) 2> /dev/null ]; then echo "Missing homebrew... aborted mysql check."; elif [ ! $(which mysql) 2> /dev/null ]; then
		slogger -st $0 "Missing MySQL ${sqlversion} database service."
		brew outdated mysql@${sqlversion} | brew upgrade
		slogger -st $0 "Installing with Homebrew..."
		brew install mysql@${sqlversion}
		slogger -st $0 "Starting the service thread..."
		brew services start mysql@${sqlversion}
		slogger -st $0 "Performing some checks..."
		mysql_upgrade -u root &
	fi
fi
sockdir=/var/run/mysqld
wd=app/Config
while [[ "$#" > 0 ]]; do case $1 in
  *.php)
    dbfile=$1
		outfile=$(echo $dbfile | cut -d . -f 1)
    source ./Scripts/cp_bkp_old.sh $wd $dbfile "${outfile}.php"
    ;;
	*.sock )
		if [ $(which mysql) 2> /dev/null ]; then
			mysql --version
		fi
		sockh=$sockdir/mysqld.sock
		#; symlink mysql socket
    slogger -st $0 "${orange}Please allow the super-user to link mysql socket to $1 ...${nc}"
    if [ -e $sockh ]; then
			ls -al $sockh
	 	else
			[ ! -d $sockdir ] && sudo mkdir -p $sockdir
	    sudo ln -vsf $1 $sockh
		fi;;
  *)
    ;;
esac; shift; done
if [ ! $docker 2> /dev/null ]; then
	if [ $(which mysql) ] && [ ! -e $sockdir/mysqld.sock ]; then
		slogger -st $0 "${orange}Warning:${nc}$sockdir/mysqld.sock not found."
	else
		slogger -st $0 "${green}Notice: mysqld.sock was found.${nc}"
	fi
fi
