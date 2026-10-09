# ==============================================================================
# STAGE 1: Base PHP 8.4 FPM environment with system dependencies & OPcache
# ==============================================================================
FROM php:8.4-fpm AS base

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Install system dependencies, Nginx, Supervisor
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    curl \
    procps \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install official PHP extension installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install required PHP extensions (PostgreSQL, OPcache, GD, etc.)
RUN install-php-extensions \
    pdo_pgsql \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    sockets \
    intl \
    gd \
    opcache

# Configure OPcache
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# ==============================================================================
# STAGE 2: Build stage (Composer & Node 22 for Vite compilation)
# ==============================================================================
FROM base AS builder

# Copy Node.js 22 & npm from official Node image
COPY --from=node:22-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install Composer dependencies
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# Install NPM dependencies
COPY package*.json ./
RUN npm ci

# Copy Application Source Code
COPY . .

# Dump optimized autoloader and run package discovery
RUN composer dump-autoload --optimize --classmap-authoritative --ignore-platform-reqs \
    && php artisan package:discover --ansi

# Build Frontend Assets
RUN npm run build

# Remove development node modules
RUN rm -rf node_modules

# ==============================================================================
# STAGE 3: Production Runner Image (Lean, Nginx + PHP-FPM + Supervisor)
# ==============================================================================
FROM base AS runner

WORKDIR /var/www/html

# Copy application & vendor directory from builder stage
COPY --chown=www-data:www-data --from=builder /var/www/html /var/www/html

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --chmod=0755 docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Ensure storage and bootstrap directories exist with proper permissions
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    /var/log/supervisor \
    && chown -R www-data:www-data storage bootstrap/cache /var/log/supervisor \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=15s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]