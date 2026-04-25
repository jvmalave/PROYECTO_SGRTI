FROM php:8.3-fpm-alpine

# Dependencias de sistema
RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    $PHPIZE_DEPS

# Instalación de extensiones PHP
RUN docker-php-ext-install pdo_pgsql zip

# Instalación de Redis
RUN pecl install redis && docker-php-ext-enable redis


#Binario de composer desde la imagen oficial a nuestro contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiamos el código
COPY . .

# Ahora sí, el comando funcionará porque 'composer' ya existe en el PATH
RUN composer install --no-dev --optimize-autoloader

# Ajuste de permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]