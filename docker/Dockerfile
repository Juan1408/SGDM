# Usa la imagen oficial de PHP con Apache (incluye el servidor web)
FROM php:8.2-apache

# Instala las extensiones de PHP necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita el módulo mod_rewrite para URLs amigables (requisito de la guía)
RUN a2enmod rewrite

# Copia una configuración personalizada de PHP (opcional, si necesitas ajustes)
# COPY php.ini /usr/local/etc/php/

# Establece el directorio de trabajo (opcional, solo para orden)
WORKDIR /var/www/html

# Expone el puerto 80 (Apache)
EXPOSE 80