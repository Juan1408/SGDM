# Dockerfile: receta para construir la imagen PHP/Apache para la aplicación SGDM
# - Instala PHP y extensiones necesarias
# - Copia el código fuente a /var/www/html
# - Expone el puerto 80

FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY ../codigo_fuente /var/www/html/
EXPOSE 80
