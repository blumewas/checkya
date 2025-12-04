#!/bin/sh
set -e

# Initialize storage directory if empty
# -----------------------------------------------------------
# If the storage directory is empty, copy the initial contents
# and set the correct permissions.
# -----------------------------------------------------------
if [ ! "$(ls -A /var/www/storage)" ]; then
  echo "Initializing storage directory..."
  cp -R /var/www/storage-init/. /var/www/storage
  chown -R www-data:www-data /var/www/storage
fi

# Remove storage-init directory
rm -rf /var/www/storage-init

echo "🔁 Running Laravel optimizations & migrations..."

php artisan migrate --force
php artisan optimize
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan config:cache
php artisan icons:cache

# Run the default command
echo "✅ Laravel ready. Starting PHP-FPM..."
exec "$@"
