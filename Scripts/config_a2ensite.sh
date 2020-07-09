#!/usr/bin/env bash
set -eu
CNF="/etc/apache2"
# DO
WWW="${1:-/var/www/localhost/htdocs}"
mkdir -p "$(dirname "$CNF")"
mkdir -p "$(dirname "$WWW")"
touch site.conf
echo -e "
<Directory \"/\">
    AllowOverride All
    Require all denied
</Directory>
<VirtualHost ${HTTPD_LISTEN}>
    DocumentRoot ${WWW}
    ServerAdmin webmaster@${SERVER_NAME}
    ServerName ${SERVER_NAME}
    ServerAlias www.${SERVER_NAME}
    <Directory \"${WWW}\">
        DirectoryIndex index.php
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog log/error.${SERVER_NAME}.log
    CustomLog log/access.${SERVER_NAME}.log combined
</VirtualHost>
ServerName ${SERVER_NAME}
" >> site.conf
cat site.conf
mv site.conf "${CNF}/conf.d/"
sed -i.old -E -e "/mod_rewrite.so/s/^#+//g" "${CNF}/httpd.conf"
grep mod_rewrite.so < "${CNF}/httpd.conf"
