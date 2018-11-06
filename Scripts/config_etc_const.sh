#!/bin/sh
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
cd app/webroot/php_cms/e13/etc/
copies=0
while [ -f constantes.properties.old-$copies ]
do
let copies++
done
cp -v constantes.properties constantes.properties.old-$copies
cp -v constantes_template.properties constantes.properties
echo "Okay, just ran the shell script. Please, review the files.\n"
cd ../../../../../
