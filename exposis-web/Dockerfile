FROM composer:2 AS composer-bin

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-fpm-alpine AS php-base
RUN apk add --no-cache libpq libzip \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS libzip-dev postgresql-dev \
    && docker-php-ext-install -j"$(nproc)" bcmath opcache pcntl pdo_pgsql zip \
    && apk del .build-deps
WORKDIR /var/www/html

FROM php-base AS app-build
COPY --from=composer-bin /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer dump-autoload --no-dev --classmap-authoritative --no-scripts \
    && php artisan package:discover --ansi \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/testing storage/framework/views storage/app/private storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

FROM app-build AS app
USER www-data
EXPOSE 9000
CMD ["php-fpm", "-F"]

FROM nginx:alpine AS web
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=app-build /var/www/html/public /var/www/html/public
WORKDIR /var/www/html
EXPOSE 80
