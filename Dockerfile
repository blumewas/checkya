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
FROM php-fpm AS intermediate-composer

WORKDIR /var/www/html

COPY . .

# Install latest composer release
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN composer install --no-dev

RUN php artisan filament:upgrade

###################################################################################
# NPM Build.                                                                      #
###################################################################################
FROM node:22-alpine AS frontend-node
LABEL stage=node

WORKDIR /var/www/html

COPY --from=intermediate-composer /var/www/html /var/www/html

# installs dependencies -> build
RUN npm install && npm run build && \
    rm -f .npmrc && \
    rm -rf node_modules

###################################################################################
# APP for AWS build                                                               #
###################################################################################
FROM php-fpm AS app
LABEL stage=app

COPY --from=frontend-node --chown=www-data:www-data /var/www/html /var/www/html

WORKDIR /var/www/html
USER www-data
