# ------------------------------------------------------
# 1) PHP-FPM BASE
# ------------------------------------------------------
FROM php:8.4-fpm-alpine AS php-base

RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    unzip \
    zip \
    oniguruma-dev \
    git \
    curl \
    bash \
    autoconf \
    g++ \
    make

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl zip pdo_mysql bcmath opcache

WORKDIR /var/www/html


# ------------------------------------------------------
# 2) Composer stage
# ------------------------------------------------------
FROM php-base AS composer-stage

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

COPY . /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-progress --no-interaction


# ------------------------------------------------------
# 3) Node asset build
# ------------------------------------------------------
FROM node:22-alpine AS node-stage

WORKDIR /build
COPY . /build

RUN npm install && npm run build


# ------------------------------------------------------
# 4) FINAL production image
# ------------------------------------------------------
FROM php-base AS app

# copy PHP (vendor, app, etc.)
COPY --from=composer-stage /var/www/html /var/www/html

# copy built front-end assets
COPY --from=node-stage /build/public /var/www/html/public

RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000
