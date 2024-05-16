# Usa la imagen oficial de PHP con el módulo de Apache
FROM php:8.2-apache

# Instala las dependencias necesarias para las extensiones intl y zip
RUN apt-get update && \
    apt-get install -y libzip-dev zlib1g-dev libicu-dev && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia el código de la aplicación
WORKDIR /var/www/html
COPY . .

# Instala las dependencias de Composer
RUN composer install --no-scripts

# Copia el archivo de entorno
COPY .env.example .env

# Genera la clave de la aplicación
RUN php artisan key:generate

# Inicia el servidor de desarrollo
CMD php artisan serve --host=0.0.0.0 --port=8000

# Expone el puerto 8000
EXPOSE 8000