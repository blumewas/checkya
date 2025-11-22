#! usr/bin/bash

USR=$(stat -c '%U' ./composer.json)
runuser -u $USR -- /bin/php /usr/local/sbin/composer install --no-dev
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan clear-compiled
php artisan view:cache
php artisan config:cache
php artisan route:cache
php artisan icons:cache
