# Utiliza la imagen oficial de PHP
FROM php:8.0-fpm

# Instala las dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    npm

# Instala las extensiones PHP necesarias
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia los archivos de la aplicación
COPY . .

# Actualiza Composer a la última versión
RUN composer self-update --2

# Instala las dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Instala las dependencias de JavaScript
RUN npm install && npm run prod

# Establece los permisos adecuados
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expone el puerto 9000
EXPOSE 9000

# Comando por defecto para iniciar PHP-FPM
CMD ["php-fpm"]
