#!/bin/bash
set -e

cd /var/www/html

echo "Starting VCMS application..."
echo "Current directory: $(pwd)"
echo "Listing files:"
ls -la | head -20

# Check if artisan exists
if [ ! -f artisan ]; then
    echo "ERROR: artisan file not found in $(pwd)"
    exit 1
fi

echo "Generating app key if needed..."
if ! grep -q "APP_KEY=" .env; then
    php artisan key:generate
fi

echo "Running composer post-autoload-dump..."
composer run-script post-autoload-dump || true

echo "Caching config..."
php artisan config:cache || true

echo "Running migrations..."
php artisan migrate --force || true

echo "Starting php-fpm..."
exec php-fpm
