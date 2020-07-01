#!/usr/bin/env bash
set -eu
CNF="/etc/apache2/conf.d"
WWW="${1:-/var/www/localhost/htdocs}"
mkdir -p "$(dirname $CNF)"
mkdir -p "$(dirname $WWW)"
touch site.conf
echo -e "
<VirtualHost ${HTTPD_LISTEN}>
    DocumentRoot ${WWW}
    ServerName www.${SERVER_NAME}
    ServerAdmin webmaster@${SERVER_NAME}
    ServerName ${SERVER_NAME}
    <Directory />
        Options +FollowSymLinks
        AllowOverride None
        Order Deny,Allow
        Deny from All
    </Directory>
    <Directory ${WWW}>
        DirectoryIndex index.php
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog /var/log/apache2/error.${SERVER_NAME}.log
    CustomLog /var/log/apache2/access.${SERVER_NAME}.log combined
</VirtualHost>

Listen ${HTTPD_LISTEN}
" >> site.conf
cat site.conf
mv site.conf "$CNF"
