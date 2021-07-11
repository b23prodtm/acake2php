<Directory "/">
    AllowOverride All
    Require all denied
</Directory>
<VirtualHost ${HTTPD_LISTEN}>
    DocumentRoot ${WWW}
    ServerAdmin webmaster@${SERVER_NAME}
    ServerName ${SERVER_NAME}
    ServerAlias www.${SERVER_NAME}
    <Directory "${WWW}">
        DirectoryIndex index.php
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog logs/error.${SERVER_NAME}.log
    TransferLog logs/access.${SERVER_NAME}.log
</VirtualHost>
ServerName ${SERVER_NAME}
LimitInternalRecursion 20
