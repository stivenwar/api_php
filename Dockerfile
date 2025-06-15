FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY ./public /var/www/html/public

# Cambiar DocumentRoot a public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Opcional: habilitar rewrite y restart apache
RUN a2enmod rewrite
