FROM php:8.2-apache

# Instalamos extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli curl

RUN a2enmod rewrite

# Copiamos el contenido
COPY ./www /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html
