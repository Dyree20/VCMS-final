#!/bin/bash
set -e

# Generate app key if .env doesn't have one
if ! grep -q "APP_KEY=" .env; then
    php artisan key:generate
fi

# Run composer post-autoload-dump
composer run-script post-autoload-dump

# Cache config
php artisan config:cache

# Run migrations if needed
php artisan migrate --force || true

exec php-fpm
