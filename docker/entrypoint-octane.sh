#!/bin/bash
set -e

echo "Starting Laravel Octane (Swoole) deployment routines..."

# Run database migrations
php artisan migrate --force --no-interaction || echo "Migration notice: DB unavailable or already up-to-date."

# Cache configuration, routes, and views
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Application initialized. Launching Octane server on port 8000..."
exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000
