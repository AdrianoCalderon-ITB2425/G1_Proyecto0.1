FROM php:8.2-apache

# Instalamos la extensión mysqli necesaria para conectar con MariaDB
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Habilitamos el mod_rewrite de Apache
RUN a2enmod rewrite

# Ajustamos permisos para que Apache pueda escribir en la carpeta de subidas
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 775 /var/www/html/uploads
