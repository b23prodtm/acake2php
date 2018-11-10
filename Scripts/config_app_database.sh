#!/bin/sh
cd app/Config
copies=0
while [ -f database.php.old-$copies ]
do
let copies++
done
cp -v database.php database.php.old-$copies
cp -v database.cms.php database.php
echo "Okay, just ran the shell script. Please, review the files.\n"
cd ../../
