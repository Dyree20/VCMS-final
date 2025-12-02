#!/bin/bash

cd /var/www/html

echo "========================================"
echo "Starting VCMS application..."
echo "========================================"

# Check if artisan exists
if [ ! -f artisan ]; then
    echo "ERROR: artisan file not found in $(pwd)"
    ls -la
    sleep 60
    exit 1
fi

# Ensure composer dependencies are installed
echo "[1/6] Checking composer dependencies..."
if [ ! -d vendor ] || [ -z "$(ls -A vendor 2>/dev/null)" ]; then
    echo "Installing composer dependencies..."
    COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist --ignore-platform-reqs 2>&1 | tail -20 || echo "Warning: composer install had issues"
fi

echo "[2/6] Running composer post-autoload-dump..."
timeout 30 composer run-script post-autoload-dump 2>&1 | tail -10 || echo "Warning: composer post-autoload-dump failed"

echo "[3/6] Generating app key if needed..."
if ! grep -q "APP_KEY=" .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate 2>&1 | tail -5 || true
fi

echo "[4/6] Caching config..."
timeout 30 php artisan config:cache 2>&1 | tail -5 || echo "Warning: config cache failed"

echo "[5/6] Running migrations..."
if [ "${SKIP_MIGRATIONS}" != "true" ]; then
    timeout 60 php artisan migrate --force 2>&1 | tail -10 || echo "Info: migrations skipped or failed (may not be critical)"
else
    echo "Skipping migrations (SKIP_MIGRATIONS=true)"
fi

echo "[6/6] Starting services..."
echo "Starting PHP-FPM..."
php-fpm -D || echo "WARNING: php-fpm failed to start"

sleep 2

echo "Starting Nginx..."
nginx -g "daemon off;" || echo "ERROR: nginx failed to start"
