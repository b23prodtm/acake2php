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
