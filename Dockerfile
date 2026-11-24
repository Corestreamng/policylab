# syntax=docker/dockerfile:1

# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Install Composer dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer*.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# Stage 3: Production runtime with FrankenPHP (PHP 8.4)
FROM dunglas/frankenphp:1-php8.4

# Install required PHP extensions for Laravel, Filament, and Excel
RUN install-php-extensions \
    intl \
    zip \
    gd \
    pdo_sqlite \
    sqlite3 \
    pcntl \
    opcache \
    bcmath

WORKDIR /app

# Copy application source
COPY . /app

# Copy built vendor and frontend assets from build stages
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build

# Ensure proper directories exist and set correct permissions
RUN mkdir -p /app/storage/framework/sessions \
    && mkdir -p /app/storage/framework/views \
    && mkdir -p /app/storage/framework/cache \
    && mkdir -p /app/storage/logs \
    && mkdir -p /app/bootstrap/cache \
    && mkdir -p /app/database \
    && touch /app/database/database.sqlite \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database \
    && chmod -R 775 /app/storage /app/bootstrap/cache /app/database

# Configure entrypoint and FrankenPHP
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV SERVER_NAME=":80"
ENV APP_ENV=production

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
