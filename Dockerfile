# ---- Build Composer Dependencies ----
FROM composer:2 AS composer_builder
WORKDIR /app

COPY ./ ./

# Install latest composer release
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN composer install --no-dev

RUN php artisan vendor:publish --tag=laravel-assets --ansi --force

# ---- PHP Runtime ----
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip unzip \
    zip \
    curl \
    git \
    && docker-php-ext-configure intl zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www/html

COPY --from=composer_builder /app ./

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
