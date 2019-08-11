#!/usr/bin/env bash
if [ $# -lt 1 ]; then echo "Usage: $0 <ServerName>
ServerName: default is 'localhost'"; fi
SRV=$1
echo -e "
<VirtualHost *:80>
    ServerAdmin webmaster@$SRV
    ServerName $SRV
    ServerAlias www.$SRV
    DocumentRoot /var/www/html/app/webroot/

    <Directory />
        Options +FollowSymLinks
        AllowOverride None
        Order Deny,Allow
        Deny from All
    </Directory>
    <Directory /var/www/html/app/webroot/>
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog /var/log/apache2/error.$SRV.log
    CustomLog /var/log/apache2/access.$SRV.log combined
</VirtualHost>" > $SRV.conf
cat $SRV.conf
mv $SRV.conf docker/apache/.
# change SERVER_NAME environment
sed -E -i.old -e /SERVER_NAME/s/"(SERVER_NAME=).*"/\\1${SRV}/g .env
