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

echo "[0/7] Checking .env file..."
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env || echo "APP_NAME=VCMS" > .env
fi

# Display current APP_KEY
echo "Current APP_KEY: $(grep APP_KEY .env | cut -d= -f2)"

echo "[1/7] Generating app key if needed..."
if ! grep -q "APP_KEY=base64:" .env; then
    echo "APP_KEY not found or invalid, generating new key..."
    php artisan key:generate --force 2>&1 | head -20
    echo "New APP_KEY: $(grep APP_KEY .env | cut -d= -f2)"
else
    echo "APP_KEY already exists"
fi

# Ensure composer dependencies are installed
echo "[2/7] Checking composer dependencies..."
if [ ! -d vendor ] || [ -z "$(ls -A vendor 2>/dev/null)" ]; then
    echo "Installing composer dependencies..."
    COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist --ignore-platform-reqs 2>&1 | tail -20 || echo "Warning: composer install had issues"
fi

echo "[3/7] Running composer post-autoload-dump..."
timeout 30 composer run-script post-autoload-dump 2>&1 | tail -10 || echo "Warning: composer post-autoload-dump failed"

echo "[4/7] Caching config..."
timeout 30 php artisan config:cache 2>&1 | tail -5 || echo "Warning: config cache failed"

echo "[5/7] Running migrations..."
if [ "${SKIP_MIGRATIONS}" != "true" ]; then
    timeout 60 php artisan migrate --force 2>&1 | tail -10 || echo "Info: migrations skipped or failed (may not be critical)"
else
    echo "Skipping migrations (SKIP_MIGRATIONS=true)"
fi

echo "[6/7] Starting services..."
echo "Starting PHP-FPM..."
php-fpm -D || echo "WARNING: php-fpm failed to start"

sleep 2

echo "[7/7] Starting Nginx..."
echo "========================================"
echo "VCMS is now running on port 8080"
echo "========================================"
nginx -g "daemon off;" || echo "ERROR: nginx failed to start"
