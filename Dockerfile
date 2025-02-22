FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Copy nginx configuration
COPY docker/nginx/nginx.conf /etc/nginx/http.d/default.conf

# Create directory for nginx pid file
RUN mkdir -p /run/nginx

# Set permissions
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

# Make the start script executable
RUN chmod +x /var/www/docker/start.sh

EXPOSE 8000

CMD ["/var/www/docker/start.sh"]