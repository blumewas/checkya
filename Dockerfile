# Build PHP FPM BASE
FROM php:8.4-fpm-alpine AS php-fpm

# Install Alpine system dependencies
RUN apk update && apk add --no-cache \
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

# Install PHP Extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl zip pdo_mysql bcmath

# ---- Build Composer Dependencies ----
FROM php-fpm AS composer_builder

WORKDIR /app

COPY ./ ./

# Install latest composer release
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN composer install --no-dev

RUN php artisan vendor:publish --tag=laravel-assets --ansi --force

FROM node:22-alpine AS frontend-node
LABEL stage=node

WORKDIR /app

COPY --from=intermediate-composer /app /app

# installs dependencies -> build
RUN npm install && npm run prod && \
    rm -f .npmrc && \
    rm -rf node_modules

###################################################################################
# APP for AWS build                                                               #
###################################################################################
FROM php-fpm AS app
LABEL stage=app

WORKDIR /var/www/html

COPY --from=frontend-node --chown=www-data:www-data /app /app

USER www-data
