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
    cp .env.example .env || true
fi

# Debug: Show environment variables
echo "DEBUG - Environment variables:"
echo "DB_HOST=${DB_HOST}"
echo "DB_PORT=${DB_PORT}"
echo "DB_DATABASE=${DB_DATABASE}"
echo "DB_USERNAME=${DB_USERNAME}"
echo "DB_PASSWORD=${DB_PASSWORD:0:10}***" # Show first 10 chars of password

# Ensure required environment variables are set in .env for Railway/production
if [ -n "$DB_HOST" ]; then
    echo "Setting database configuration from environment variables..."
    # Use PHP to safely update .env (more reliable than sed)
    php -r "
    \$env = file_get_contents('.env');
    \$env = preg_replace('/^DB_HOST=.*/m', 'DB_HOST=' . getenv('DB_HOST'), \$env);
    \$env = preg_replace('/^DB_PORT=.*/m', 'DB_PORT=' . getenv('DB_PORT'), \$env);
    \$env = preg_replace('/^DB_DATABASE=.*/m', 'DB_DATABASE=' . getenv('DB_DATABASE'), \$env);
    \$env = preg_replace('/^DB_USERNAME=.*/m', 'DB_USERNAME=' . getenv('DB_USERNAME'), \$env);
    \$env = preg_replace('/^DB_PASSWORD=.*/m', 'DB_PASSWORD=' . getenv('DB_PASSWORD'), \$env);
    file_put_contents('.env', \$env);
    echo 'Environment variables synced to .env\n';
    " 2>&1 || echo "Warning: PHP .env update failed, environment variables will be used directly"
fi

# Display current config
echo "DB Configuration in .env:"
grep "^DB_" .env || echo "No DB config found"
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

echo "[3/7] Caching config..."
# Skip config caching in Railway - we need dynamic env vars for database connection
# Config caching would freeze DB credentials at build time, breaking Railway deployments
# if [ "${SKIP_CONFIG_CACHE}" != "true" ]; then
#     rm -rf bootstrap/cache/config.php 2>/dev/null || true
#     timeout 30 php artisan config:cache 2>&1 | tail -5 || echo "Warning: config cache failed"
# else
#     echo "Skipping config cache (SKIP_CONFIG_CACHE=true)"
# fi
echo "Skipping config cache (using runtime env vars for Railway compatibility)"

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
