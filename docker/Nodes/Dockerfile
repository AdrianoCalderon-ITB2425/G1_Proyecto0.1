FROM php:8.2-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN a2enmod rewrite

RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 775 /var/www/html/uploads
RUN { \
    echo 'opcache.enable=0'; \
    echo 'opcache.enable_cli=0'; \
} > /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
