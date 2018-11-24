#!/bin/bash
sqlversion="5.7"
if [ ! $(which mysql) > /dev/null ]; then
	echo -e "Missing MySQL ${sqlversion} database service."
	brew outdated mysql@${sqlversion} | brew upgrade
	echo -e "Installing with Homebrew..."
	brew install mysql@${sqlversion}
	echo -e "Starting the service thread..."
	brew services start mysql@${sqlversion}
	echo -e "Performing some checks..."
	mysql_upgrade -u root &
else
  mysql --version
fi
echo -e "
${red}If the Error: 'Database connection \"Mysql\" is missing, or could not be created'${nc}
 shows up, please check up your ${cyan}TEST_DATABASE_NAME=$TEST_DATABASE_NAME${nc} environment variable (set up is above in this shell script or in web node settings).
     Log into the SQL shell ${green}mysql -u root${nc} and check if you can do : ${green}use $TEST_DATABASE_NAME${nc}.
     Run the socket fixup script with arguments
${cyan}
      ./migrate-database.sh -Y
      brew services restart mysql@${sqlversion}
${nc}"
if [ ! -h /var/mysql/mysql.sock ]; then
	echo -e "${orange}We must fix up : ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/mysql/mysql.sock' (2)${nc}"
	echo -e "${blue}Run the command ${green}./migrate-database.sh -Y${nc}\n"
fi
while [[ "$#" > 0 ]]; do case $1 in
  *.php)
    dbfile=$1
    wd="app/Config"
    source ./Scripts/cp_bkp_old.sh $wd $dbfile "database.php"
    ;;
  -[yY]*)
    #; symlink mysql socket with php
    echo "Please allow the super-user to link mysql socket to php ..."
    sudo mkdir -p /var/mysql
    if [ -h /var/mysql/mysql.sock ]; then ls -al /var/mysql/mysql.sock; else sudo ln -vs /tmp/mysql.sock /var/mysql/mysql.sock; fi;
    ;;
  *)
    ;;
esac; shift; done