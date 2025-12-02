FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    curl \
    git \
    wget \
    ca-certificates \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# List files to debug
RUN ls -la /var/www/html | grep -E "(artisan|composer)"

# Make artisan executable
RUN chmod +x /var/www/html/artisan

# Install PHP dependencies (skip scripts)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Create .env if not exists
RUN if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || echo "APP_NAME=VCMS\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_URL=http://localhost" > .env; fi

# Generate app key
RUN php artisan key:generate --force

# Run composer post-autoload-dump
RUN composer run-script post-autoload-dump

# Cache config
RUN php artisan config:cache

EXPOSE 9000

CMD ["php-fpm"]
