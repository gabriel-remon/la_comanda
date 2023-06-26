# Usar la imagen oficial de PHP 7.4 con Apache incluido
FROM php:7.4-apache

# Instalar las extensiones de PHP que puedas necesitar
RUN apt-get update && apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    zip \
    curl \
    unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd mbstring zip exif pcntl \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers

# Copiar el directorio de la aplicación en el contenedor
COPY . /var/www/html/

# Dar permiso al directorio
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Cambiar el directorio de trabajo
WORKDIR /var/www/html

# Instalar las dependencias de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Exponer el puerto 80 para Apache y arrancar Apache en primer plano
EXPOSE 80
CMD ["apache2-foreground"]
