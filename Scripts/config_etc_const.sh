#!/bin/sh
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
