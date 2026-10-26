# Usa la imagen oficial de PHP con Apache (incluye el servidor web)
FROM php:8.2.29-apache

# Instala las extensiones de PHP necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita el módulo mod_rewrite para URLs amigables (requisito de la guía)
RUN a2enmod rewrite

# El docroot se limita a la carpeta pública: no se sirven configuración,
# modelos, vistas ni tests. Options -Indexes evita el listado de directorios.
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Copia una configuración personalizada de PHP (opcional, si necesitas ajustes)
# COPY php.ini /usr/local/etc/php/

# Establece el directorio de trabajo (opcional, solo para orden)
WORKDIR /var/www/html

# Expone el puerto 80 (Apache)
EXPOSE 80