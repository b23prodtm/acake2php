#!/bin/bash
dbfile=$1
wd="app/Config"
source ./Scripts/cp_bkp_old.sh $wd $dbfile "database.php"
#; symlink mysql socket with php
sudo mkdir -p /var/mysql
if [ -h /var/mysql/mysql.sock ]; then ls -al /var/mysql/mysql.sock; else sudo ln -vs /tmp/mysql.sock /var/mysql/mysql.sock; fi;
