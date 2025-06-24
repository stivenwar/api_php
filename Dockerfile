FROM php:8.2-apache

# Instalar extensiones necesarias
#RUN docker-php-ext-install pdo pdo_mysql

# Instalar extensiones PDO para MySQL y PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql


# Copiar todo el proyecto al contenedor
COPY . /var/www/html

# Cambiar DocumentRoot a la carpeta public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Habilitar el módulo rewrite
RUN a2enmod rewrite

# Configurar Apache para escuchar en el puerto que Render asigna
# ENV APACHE_LISTEN_PORT=${PORT:-8080}

# RUN sed -ri "s/Listen 80/Listen ${APACHE_LISTEN_PORT}/g" /etc/apache2/ports.conf \
#     && sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${APACHE_LISTEN_PORT}>/g" /etc/apache2/sites-available/000-default.conf

# EXPOSE ${APACHE_LISTEN_PORT}
