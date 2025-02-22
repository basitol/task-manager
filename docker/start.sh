# docker/start.sh
#!/bin/sh

# Generate Laravel app key if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Start nginx
nginx

# Start PHP-FPM
php-fpm§