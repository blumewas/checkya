#!/bin/sh
set -e

echo "🔁 Running Laravel optimizations & migrations..."

php artisan migrate --force
php artisan optimize
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan config:cache

echo "✅ Laravel ready. Starting PHP-FPM..."
exec php-fpm
