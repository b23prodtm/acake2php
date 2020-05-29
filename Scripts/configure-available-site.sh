#!/usr/bin/env bash
if [ $# -lt 1 ]; then echo "Usage: $0 <ServerName> [wildcard-ip:port]"; fi
if [ "$#" > 1 ]; then HTTPD_LISTEN=$2; fi
SERVER_NAME=${SERVER_NAME:-$1}
HTTPD_LISTEN=${HTTPD_LISTEN:-'*:80'}
echo -e "
<VirtualHost ${HTTPD_LISTEN}>
    DocumentRoot /var/www/html/app/webroot/
    ServerAlias www.${SERVER_NAME}
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
    ErrorLog /var/log/apache2/error.${SERVER_NAME}.log
    CustomLog /var/log/apache2/access.${SERVER_NAME}.log combined
</VirtualHost>

ServerAdmin webmaster@${SERVER_NAME}
ServerName ${SERVER_NAME}
ServerSignature Off
ServerTokens Prod
" > ${SERVER_NAME}.conf
# change SERVER_NAME environment
sed -E -i.old -e /SERVER_NAME/s/"(SERVER_NAME=).*"/\\1${SERVER_NAME}/g common.env
