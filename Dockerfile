FROM php:8.2-fpm

WORKDIR /var/www/html

# Install minimal system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    curl \
    nginx \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    zip \
    xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy only composer files first
COPY composer.json composer.lock* ./

# Pre-download composer dependencies
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --ignore-platform-reqs \
    || echo "Warning: Initial composer install had issues"

# Copy remaining application files
COPY . .

# Ensure vendor exists
RUN test -d vendor || mkdir -p vendor

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Create .env
RUN if [ ! -f .env ]; then cp .env.example .env || echo "APP_NAME=VCMS" > .env; fi

# Configure Nginx
RUN mkdir -p /var/run/nginx

# Create Nginx config
RUN cat > /etc/nginx/sites-available/default <<'EOF'
server {
    listen 8080;
    server_name _;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080

# Use bash explicitly
CMD ["/bin/bash", "/usr/local/bin/docker-entrypoint.sh"]
