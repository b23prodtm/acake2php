#!/usr/bin/env bash
[ $# -lt 1 ] && echo "Usage : $0 [--docker] [<file.template.or.php.or.sock>]..." && exit 1
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
sqlversion="5.7"
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
# shellcheck source=lib/parsing.sh
. "${TOPDIR}/Scripts/lib/parsing.sh"
docker=$(parse_arg "--docker" "$@")
if [ -n "$docker" ]; then
	bash -c "./Scripts/start_daemon.sh ${docker}"
else
	if [ -z "$(command -v brew)" ]; then
		echo "Missing homebrew... aborted mysql check.";
	elif [ -z "$(command -v mysql)" ]; then
		slogger -st "$0" "Missing MySQL ${sqlversion} database service."
		brew outdated mysql@${sqlversion} | brew upgrade
		slogger -st "$0" "Installing with Homebrew..."
		brew install mysql@${sqlversion}
		slogger -st "$0" "Starting the service thread..."
		brew services start mysql@${sqlversion}
		slogger -st "$0" "Performing some checks..."
		mysql_upgrade -u root &
	fi
fi
sockdir=/var/run/mysqld
while [[ "$#" -gt 0 ]]; do case $1 in
	*.php|*.template)
		dbfile=$1
		file=$(echo "$dbfile" | cut -d . -f 1)
		# shellcheck source=cp_bkp_old.sh
		. "${TOPDIR}/Scripts/cp_bkp_old.sh" "$TOPDIR" "$dbfile" "${file}.php";;
	*.sock)
		if [ -n "$(command -v mysql)" ]; then
			mysql --version
		fi
		sockh=$sockdir/mysqld.sock
		# shellcheck disable=SC2154
		slogger -st "$0" "${orange}Try $sockh to symlink the socket $1...${nc}"
                if [ -e $sockh ]; then
			ls -al $sockh
		else
			# shellcheck disable=SC2154
			slogger -st "$0" "${orange}Please allow the super-user to link mysql socket to $1 ...${nc}"
			[ ! -d $sockdir ] && sudo mkdir -p $sockdir
	                sudo ln -vsf "$1" $sockh
		fi;;
  *)
    ;;
esac; shift; done
if [ -z "$docker" ]; then
	if [ -n "$(command -v mysql)" ] && [ ! -e $sockdir/mysqld.sock ]; then
		slogger -st "$0" "${orange}Warning:${nc}$sockdir/mysqld.sock not found."
	else
		# shellcheck disable=SC2154
		slogger -st "$0" "${green}Notice: mysqld.sock was found.${nc}"
	fi
fi
