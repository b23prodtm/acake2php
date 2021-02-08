#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
. init_functions .
log_daemon_msg "To add more VirtualHost $HTTPD_LISTEN, ${BASH_SOURCE[0]} <directory>"
CNF="/etc/apache2"
WWW="${1:-$TOPDIR/app/webroot}"
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
    ErrorLog logs/error.${SERVER_NAME}.log
    TransferLog logs/access.${SERVER_NAME}.log
</VirtualHost>
ServerName ${SERVER_NAME}
" >> site.conf
cat site.conf
mv site.conf "${CNF}/conf.d/"
log_daemon_msg "SSL VirtualHost"
touch ssl_site.conf
echo -e "
<VirtualHost ${HTTPD_LISTEN/:80/:443}>
    DocumentRoot ${WWW}
    ServerAdmin webmaster@${SERVER_NAME}
    ServerName ${SERVER_NAME}:443
    ServerAlias www.${SERVER_NAME}:443
    <Directory \"${WWW}\">
        DirectoryIndex index.php
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog logs/ssl_error.${SERVER_NAME}.log
    TransferLog logs/ssl_access.${SERVER_NAME}.log
    SSLEngine on
    SSLCertificateFile /etc/ssl/apache2/server.pem
    SSLCertificateKeyFile /etc/ssl/apache2/server.key
    #SSLCertificateChainFile /etc/ssl/apache2/server-ca.pem
    #SSLCACertificatePath /etc/ssl/apache2/ssl.crt
    #SSLCACertificateFile /etc/ssl/apache2/ssl.crt/ca-bundle.pem
</VirtualHost>
" >> ssl_site.conf
cat ssl_site.conf
mv ssl_site.conf "${CNF}/conf.d/"
log_daemon_msg "Enable mod_rewrite"
sed -i.old -E -e "/mod_rewrite.so/s/^#+//g" "${CNF}/httpd.conf"
grep mod_rewrite.so < "${CNF}/httpd.conf"
log_daemon_msg "Add /etc/hosts $SERVER_NAME"
sed -i "/127.0.0.1/s/(localhost)/\\1 ${SERVER_NAME} www.${SERVER_NAME}/" /etc/hosts
