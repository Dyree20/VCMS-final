#!/bin/bash
set -e

cd /var/www/html

echo "Starting VCMS application..."

# Check if artisan exists
if [ ! -f artisan ]; then
    echo "ERROR: artisan file not found in $(pwd)"
    exit 1
fi

# Ensure composer dependencies are installed
echo "Checking composer dependencies..."
if [ ! -d vendor ] || [ -z "$(ls -A vendor)" ]; then
    echo "Installing composer dependencies..."
    COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist --ignore-platform-reqs || echo "Warning: composer install had issues"
fi

echo "Running composer post-autoload-dump..."
timeout 30 composer run-script post-autoload-dump || echo "Warning: composer post-autoload-dump timed out or failed"

echo "Generating app key if needed..."
if ! grep -q "APP_KEY=" .env; then
    echo "Generating application key..."
    php artisan key:generate || true
fi

echo "Caching config..."
timeout 30 php artisan config:cache || echo "Warning: config cache failed"

echo "DB_CONNECTION=${DB_CONNECTION:-pgsql}"
if [ "${SKIP_MIGRATIONS}" != "true" ]; then
    echo "Running migrations..."
    timeout 60 php artisan migrate --force || echo "Warning: migrations skipped"
fi

echo "Starting services with supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
