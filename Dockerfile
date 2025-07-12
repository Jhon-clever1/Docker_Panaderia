FROM php:8.1-apache

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && docker-php-ext-enable mysqli

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar los archivos de la aplicación al contenedor
COPY . /var/www/html/

# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html/

# Exponer el puerto 80
EXPOSE 80