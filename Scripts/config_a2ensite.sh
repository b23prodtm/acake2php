#!/usr/bin/env bash
set -eu
CNF="${1:-/etc/apache2/sites-available/}"
touch site.conf
echo -e "
<VirtualHost ${HTTPD_LISTEN}>
    DocumentRoot /var/www/html/app/webroot/
    ServerName www.${SERVER_NAME}
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
ServerTokens Prod" >> site.conf
cat site.conf
mapfile -t cnf< <(find $CNF -name "*.conf")
mv site.conf "$CNF/00${#cnf}-${SERVER_NAME}.conf"
a2ensite "00${#cnf}-${SERVER_NAME}"
