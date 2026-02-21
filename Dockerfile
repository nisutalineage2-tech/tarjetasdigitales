FROM php:8.2-apache

RUN a2enmod rewrite

# Copiamos solo el contenido de la carpeta www
COPY ./www /var/www/html/

# Aseguramos la carpeta de categorías y permisos
RUN chown -R www-data:www-data /var/www/html
