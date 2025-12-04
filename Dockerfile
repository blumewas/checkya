# ------------------------------------------------------
# 1) PHP-FPM BASE
# ------------------------------------------------------
FROM php:8.4-fpm-alpine AS php-base

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

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl zip pdo_mysql bcmath opcache mbstring exif pcntl

WORKDIR /var/www/html


# ------------------------------------------------------
# 2) Composer stage
# ------------------------------------------------------
FROM php-base AS composer-stage

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
RUN composer install --no-dev --optimize-autoloader --no-scripts


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

# Ensure permissions
RUN chown -R www-data:www-data /var/www/html

# Entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]
