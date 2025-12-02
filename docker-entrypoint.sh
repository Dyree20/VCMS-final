#!/bin/bash

cd /var/www/html

echo "========================================"
echo "Starting VCMS application..."
echo "========================================"

# Fix permissions early
echo "Fixing file permissions..."
chmod -R 755 /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

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
    echo "  DB_HOST: $DB_HOST"
    echo "  DB_PORT: $DB_PORT"
    echo "  DB_DATABASE: $DB_DATABASE"
    echo "  DB_USERNAME: $DB_USERNAME"
    echo "  DB_PASSWORD: ${DB_PASSWORD:0:5}*** (length: ${#DB_PASSWORD})"
    
    # Use a temporary file to safely handle passwords with special characters
    {
        while IFS='=' read -r key value || [ -n "$key" ]; do
            # Skip empty lines and comments
            if [ -z "$key" ] || [[ "$key" =~ ^# ]]; then
                [ -z "$key" ] && echo "" || echo "$key=$value"
                continue
            fi
            
            case "$key" in
                DB_HOST) [ -n "$DB_HOST" ] && echo "DB_HOST=$DB_HOST" || echo "$key=$value" ;;
                DB_PORT) [ -n "$DB_PORT" ] && echo "DB_PORT=$DB_PORT" || echo "$key=$value" ;;
                DB_DATABASE) [ -n "$DB_DATABASE" ] && echo "DB_DATABASE=$DB_DATABASE" || echo "$key=$value" ;;
                DB_USERNAME) [ -n "$DB_USERNAME" ] && echo "DB_USERNAME=$DB_USERNAME" || echo "$key=$value" ;;
                DB_PASSWORD) [ -n "$DB_PASSWORD" ] && echo "DB_PASSWORD=$DB_PASSWORD" || echo "$key=$value" ;;
                *) echo "$key=$value" ;;
            esac
        done < .env
    } > .env.tmp && mv .env.tmp .env
    echo "✓ Environment variables synced to .env"
else
    echo "⚠ No DB_HOST environment variable - using .env defaults"
fi

echo "DB Configuration in .env (final):"
grep "^DB_" .env | sed 's/DB_PASSWORD=.*/DB_PASSWORD=***/' || echo "No DB config found"

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
