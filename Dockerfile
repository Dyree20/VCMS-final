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

# Copy ALL application files first (before composer install)
COPY . .

# Install PHP dependencies with --no-scripts to skip post-autoload-dump
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Create .env if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || echo "APP_NAME=VCMS\nAPP_ENV=production\nAPP_DEBUG=false\nAPP_URL=http://localhost" > .env; fi

# Generate application key (required before running artisan commands)
RUN php artisan key:generate

# Now run composer post-autoload-dump script after artisan exists
RUN composer run-script post-autoload-dump

# Cache config and routes
RUN php artisan config:cache && php artisan route:cache

EXPOSE 9000

CMD ["php-fpm"]
