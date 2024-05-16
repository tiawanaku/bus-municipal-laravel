# CARGAMOS IMAGEN DE PHP MODO ALPINE SUPER REDUCIDA
FROM elrincondeisma/octane:latest

RUN apk add --no-cache curl php php-json php-phar php-mbstring php-dom php-xml php-openssl php-tokenizer php-fileinfo php-zlib php-curl php-ctype php-session php-iconv php-json php-posix php-pcntl php-xmlwriter php-pdo php-simplexml php-tokenizer php-xmlreader php-sockets php-mysqli php-sodium php-pdo_mysql

RUN curl -sS https://getcomposer.org/installer​ | php -- \
     --install-dir=/usr/local/bin --filename=composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=spiralscout/roadrunner:2.4.2 /usr/bin/rr /usr/bin/rr

WORKDIR /app
COPY . .
RUN rm -rf /app/vendor
RUN rm -rf /app/composer.lock
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN composer require laravel/octane spiral/roadrunner
COPY .env.example .env
RUN mkdir -p /app/storage/logs
RUN php artisan cache:clear
RUN php artisan view:clear
RUN php artisan config:clear
RUN php artisan octane:install --server="swoole"
CMD php artisan octane:start --server="swoole" --host="0.0.0.0"

EXPOSE 8000