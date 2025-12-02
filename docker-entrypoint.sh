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
    echo "Syncing Railway environment variables to .env..."
    # Use a temporary file to safely handle passwords with special characters
    {
        while IFS='=' read -r key value; do
            case "$key" in
                DB_HOST) echo "DB_HOST=$DB_HOST" ;;
                DB_PORT) echo "DB_PORT=$DB_PORT" ;;
                DB_DATABASE) echo "DB_DATABASE=$DB_DATABASE" ;;
                DB_USERNAME) echo "DB_USERNAME=$DB_USERNAME" ;;
                DB_PASSWORD) echo "DB_PASSWORD=$DB_PASSWORD" ;;
                *) [ -n "$key" ] && echo "$key=$value" ;;
            esac
        done < .env
    } > .env.tmp && mv .env.tmp .env
    echo "Environment variables synced"
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
    echo "Attempting to run migrations..."
    # Try migrations, capture success/failure
    if timeout 60 php artisan migrate --force 2>&1 | tee /tmp/migration.log; then
        echo "Migrations completed successfully"
        # If migrations succeeded, switch back to database sessions
        sed -i.bak 's/^SESSION_DRIVER=file/SESSION_DRIVER=database/g' .env || true
        rm -f .env.bak
    else
        echo "Migrations failed or database unavailable - continuing with file-based sessions"
    fi
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
