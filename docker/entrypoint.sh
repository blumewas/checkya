#!/bin/sh
set -e

echo "🔁 Running Laravel optimizations & migrations..."

ls
ls /var
ls /var/www
ls /var/www/html

php artisan migrate --force
php artisan optimize
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan config:cache

echo "✅ Laravel ready. Starting PHP-FPM..."
exec php-fpm
