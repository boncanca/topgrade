# ==============================================================================
# STAGE 1: Base PHP 8.4 environment with system dependencies & Swoole
# ==============================================================================
FROM php:8.4-cli AS base

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Install basic system utilities
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    procps \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install official PHP extension installer
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install required PHP extensions (including GD, GMP, OPcache & Swoole for PHP 8.4)
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    sockets \
    intl \
    gd \
    gmp \
    opcache \
    swoole

# ==============================================================================
# STAGE 2: Build stage (Composer, Node.js 22, Vite compilation)
# ==============================================================================
FROM base AS builder

# Copy Node.js 22 & npm directly from official Node image
COPY --from=node:22-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy Composer files
COPY composer.json composer.lock ./

# Install Composer vendor packages safely
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# Copy NPM package definitions & install Node dependencies
COPY package*.json ./
RUN npm ci

# Copy Application Source Code
COPY . .

# Generate Autoloader FIRST (creates vendor/autoload.php), then run package discovery
RUN composer dump-autoload --optimize --classmap-authoritative --ignore-platform-reqs \
    && php artisan package:discover --ansi

# Build Frontend Assets (Vite)
RUN npm run build

# Remove development node modules (not needed in runner stage)
RUN rm -rf node_modules

# ==============================================================================
# STAGE 3: Production Runner Image (Lean, Safe, Non-Root)
# ==============================================================================
FROM base AS runner

# Create non-root system user & group
RUN groupadd -g 1000 laravel && useradd -u 1000 -ms /bin/bash -g laravel laravel

WORKDIR /var/www

# Copy application & vendor directory from builder stage with proper ownership
COPY --chown=laravel:laravel --from=builder /var/www /var/www

# Ensure required storage and cache directories exist with correct permissions
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R laravel:laravel storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Startup script (Runs at container boot with runtime environment variables)
RUN echo '#!/bin/bash' > /start.sh && \
    echo 'set -e' >> /start.sh && \
    echo 'php artisan migrate --force' >> /start.sh && \
    echo 'php artisan config:cache' >> /start.sh && \
    echo 'php artisan route:cache' >> /start.sh && \
    echo 'php artisan view:cache' >> /start.sh && \
    echo 'php artisan event:cache 2>/dev/null || true' >> /start.sh && \
    echo 'php artisan icons:cache 2>/dev/null || true' >> /start.sh && \
    echo 'php artisan filament:optimize 2>/dev/null || true' >> /start.sh && \
    echo 'exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000' >> /start.sh && \
    chown laravel:laravel /start.sh && \
    chmod +x /start.sh

# Expose Port 8000 for Dokploy/Traefik routing
EXPOSE 8000

# Health Check using Laravel /up endpoint
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:8000/up || exit 1

# Switch to non-root user for execution
USER laravel

CMD ["/start.sh"]