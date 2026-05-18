FROM php:8.4-fpm

# System deps + nginx + node
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx supervisor \
        libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libsqlite3-dev \
        unzip git curl ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite zip bcmath opcache \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# PHP ini hardening + opcache + uploads
COPY docker/php-security.ini /usr/local/etc/php/conf.d/zz-security.ini
COPY docker/php-opcache.ini  /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php-uploads.ini  /usr/local/etc/php/conf.d/zz-uploads.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .

RUN npm run build && rm -rf node_modules

RUN composer dump-autoload --optimize

# nginx + supervisord + php-fpm pool config
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chmod 666 /var/www/html/database/database.sqlite \
    && chown -R www-data:www-data /var/www/html/database \
    && mkdir -p /var/log/supervisor /run/nginx /run/php

COPY docker-start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

EXPOSE 8080

CMD ["/var/www/html/start.sh"]
