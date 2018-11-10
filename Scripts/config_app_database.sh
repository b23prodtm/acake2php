#!/bin/sh
cd app/Config
cp -v database.php database.php.old
cp -v $dbfile database.php
echo "Okay, just run the shell script. Please, review the files.\n"
sudo mkdir -p /var/mysql
sudo ln -s /tmp/mysql.sock /var/mysql/mysql.sock
cd ../../
