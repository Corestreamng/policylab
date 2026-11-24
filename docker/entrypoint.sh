#!/bin/sh
set -e

# Ensure SQLite file exists with proper permissions
mkdir -p /app/database
touch /app/database/database.sqlite
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database
chmod -R 775 /app/storage /app/bootstrap/cache /app/database

# Run migrations and seeders safely
php artisan migrate --force --no-interaction || true
php artisan db:seed --force --no-interaction || true

# Clear and optimize application cache
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

if command -v docker-php-entrypoint >/dev/null 2>&1; then
    exec docker-php-entrypoint "$@"
else
    exec "$@"
fi
