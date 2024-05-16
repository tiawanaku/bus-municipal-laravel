# CARGAMOS IMAGEN DE PHP MODO ALPINE SUPER REDUCIDA
FROM elrincondeisma/octane:latest

# Instalamos Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiamos Composer desde la imagen oficial de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiamos RoadRunner desde la imagen oficial de Spiral Scout
COPY --from=spiralscout/roadrunner:2.4.2 /usr/bin/rr /usr/bin/rr

# Establecemos el directorio de trabajo
WORKDIR /app

# Copiamos el código fuente
COPY . .

# Eliminamos directorios innecesarios
RUN rm -rf /app/vendor
RUN rm -rf /app/composer.lock

# Instalamos dependencias
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Requerimos paquetes adicionales
RUN composer require laravel/octane spiral/roadrunner

# Copiamos el archivo de entorno de ejemplo
COPY .env.example .env

# Creamos directorios necesarios
RUN mkdir -p /app/storage/logs

# Limpiamos la caché de Laravel
RUN php artisan cache:clear
RUN php artisan view:clear
RUN php artisan config:clear

# Instalamos y configuramos Octane para Swoole
RUN php artisan octane:install --server="swoole"

# Comando de inicio
CMD php artisan octane:start --server="swoole" --host="0.0.0.0"

EXPOSE 8000