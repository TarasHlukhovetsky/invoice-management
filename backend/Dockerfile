FROM composer:2 AS composer_dependencies

WORKDIR /var/www/html
COPY composer.json composer.lock* ./
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader

FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
        libzip-dev \
        icu-dev \
        bash \
    && docker-php-ext-install \
        pdo_mysql \
        bcmath \
        intl \
        zip \
        opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
COPY --from=composer_dependencies /var/www/html/vendor ./vendor

RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["php-fpm"]
