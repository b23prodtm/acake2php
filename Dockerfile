FROM arm32v7/php:${DOCKER_PHP_VERSION}-apache 
ENV DB
ENV MYSQL_SERVICE_HOST
ENV MYSQL_SERVICE_PORT
ENV TEST_MYSQL_SERVICE_HOST
ENV TEST_MYSQL_SERVICE_PORT
ENV DATABASE_SERVICE_NAME
ENV DATABASE_NAME
ENV DATABASE_USER
ENV DATABASE_PASSWORD
ENV TEST_DATABASE_USER
ENV TEST_DATABASE_PASSWORD

# ARG CAKEPHP_SECRET_TOKEN
# ARG CAKEPHP_SECRET_SALT
# ARG CAKEPHP_SECURITY_CIPHER_SEED

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN apt-get update -yqq \
  && apt-get install -yqq --no-install-recommends \
    git \
    zip \
    unzip \
    libicu-dev \
    libpq-dev \
    libmcrypt-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
  && rm -rf /var/lib/apt/lists

# Enable PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli \
    intl \
    mbstring \
    pcntl \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    zip \
    opcache \
  && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
  && docker-php-ext-install -j$(nproc) iconv \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Add cake and composer command to system path
ENV PATH="${PATH}:/var/www/html/lib/Cake/Console"
ENV PATH="${PATH}:/var/www/html/app/Vendor/bin"

# COPY apache site.conf file
COPY docker/apache/site-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/b23prodtm.info.conf /etc/apache2/sites-available/b23prodtm.info.conf

# Add site conf to available domains
RUN a2ensite b23prodtm.info

# Add SSL module
RUN a2enmod ssl

# Copy the source code into /var/www/html/ inside the image
COPY . /var/www/html/

# Configure the application
WORKDIR /var/www/html/

# Install all PHP dependencies
RUN composer install --no-interaction

# Configuration 
RUN ["bash", "-c", "./configure.sh", "--openshift" ,"-d", "-u", "-v"]
# Password Hash Verbose
# RUN cat app/webroot/php_cms/e13/etc/export_hash_password.sh | awk -F= '{print $2}' | tail -n 1

# Set default working directory
WORKDIR /var/www/html/app

# Create tmp directory and make it writable by the web server
RUN mkdir -p \
    tmp/cache/models \
    tmp/cache/persistent \
  && chown -R :www-data \
    tmp \
  && chmod -R 770 \
    tmp

# Change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Enable Apache modules and restart
RUN a2enmod rewrite \
  && service apache2 restart

EXPOSE 80
