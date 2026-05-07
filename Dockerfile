FROM php:8.2-apache

# Install system dependencies + Node.js
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libsqlite3-dev unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite zip bcmath \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 20 LTS for Vite build
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Set Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy package files and install npm dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy application
COPY . .

# Build Vite assets for production
RUN npm run build

# Post-install scripts
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure database directory is writable
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chmod 666 /var/www/html/database/database.sqlite \
    && chown www-data:www-data /var/www/html/database/database.sqlite

# Create startup script that runs migrations + seed then starts Apache
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Create .env from example if not exists\n\
if [ ! -f /var/www/html/.env ]; then\n\
  cp /var/www/html/.env.example /var/www/html/.env\n\
fi\n\
\n\
# Generate app key if not set\n\
php artisan key:generate --force 2>/dev/null || true\n\
\n\
# Clear caches for fresh start\n\
php artisan config:clear\n\
php artisan cache:clear 2>/dev/null || true\n\
\n\
# Run migrations and seed\n\
php artisan migrate --force --seed\n\
\n\
# Cache config and routes for performance\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Create storage link\n\
php artisan storage:link 2>/dev/null || true\n\
\n\
# Start Apache\n\
exec apache2-foreground\n\
' > /var/www/html/start.sh && chmod +x /var/www/html/start.sh

EXPOSE 80

CMD ["/var/www/html/start.sh"]
