#!/bin/bash
set -e

cd /var/www/html

echo "Starting VCMS application..."

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

echo "Attempting migrations (this may fail if DB not ready)..."
php artisan migrate --force || echo "Migration skipped (DB may not be ready yet)"

echo "Starting services with supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
