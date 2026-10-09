#!/bin/sh
set -e

echo "Running TopGrade London FC startup deployment routines..."

# Ensure required storage and cache directories exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Ensure web server ownership and write permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure storage symlink exists
php /var/www/html/artisan storage:link --no-interaction || true

# Run database migrations automatically
echo "Running database migrations..."
php /var/www/html/artisan migrate --force --no-interaction

# Synchronize deterministic administrator accounts
echo "Synchronizing administrator accounts..."
php /var/www/html/artisan db:seed --class=UserSeeder --force --no-interaction

# Optimize application caches for production
echo "Caching Laravel configuration, routes, views, and events..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache
php /var/www/html/artisan event:cache

echo "Startup complete. Starting Nginx, PHP-FPM, queue worker, and scheduler..."
exec "$@"
