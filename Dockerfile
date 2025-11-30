# ---- Build Composer Dependencies ----
FROM composer:2 AS composer_builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

COPY . .

RUN php artisan vendor:publish --tag=laravel-assets --ansi --force

# ---- PHP Runtime ----
FROM php:8.4-fpm-alpine

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

WORKDIR /var/www/html

# Copy Laravel application from builder
COPY --from=composer_builder /app ./

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
