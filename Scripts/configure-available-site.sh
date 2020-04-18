#!/usr/bin/env bash
if [ $# -lt 1 ]; then echo "Usage: $0 <ServerName>
ServerName: default is 'localhost'"; fi
SRV=$1
echo -e "
<VirtualHost *:80>
    DocumentRoot /var/www/html/app/webroot/
    ServerAlias www.$SRV
    <Directory />
        Options +FollowSymLinks
        AllowOverride None
        Order Deny,Allow
        Deny from All
    </Directory>
    <Directory /var/www/html/app/webroot/>
        DirectoryIndex index.php
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog /var/log/apache2/error.$SRV.log
    CustomLog /var/log/apache2/access.$SRV.log combined
</VirtualHost>

ServerAdmin webmaster@$SRV
ServerName $SRV
ServerSignature Off
ServerTokens Prod
" > $SRV.conf
# change SERVER_NAME environment
sed -E -i.old -e /SERVER_NAME/s/"(SERVER_NAME=).*"/\\1${SRV}/g common.env
