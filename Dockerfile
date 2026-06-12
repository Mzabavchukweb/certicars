FROM php:8.4-fpm

# System deps + nginx + node + headless Chromium + ffmpeg.
# Chromium is required by spatie/browsershot for the public CertiCheck PDF
# brochure. DomPDF remains in-tree as a fallback if the headless render fails.
# libwebp-dev is needed so GD can encode/decode WebP — without it the
# brochure's image embedder rejects every WebP photo silently.
# ffmpeg is required by the interior 360° feature: admin uploads a slow
# pan-around video, ExtractInteriorFramesJob runs ffmpeg to slice it into a
# fixed-count frame sequence that the catalog page scrubs through (Copart-style).
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx supervisor \
        libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libsqlite3-dev libwebp-dev \
        unzip git curl ca-certificates \
        chromium \
        ffmpeg \
        fonts-liberation fonts-dejavu fontconfig \
        libnss3 libatk1.0-0 libatk-bridge2.0-0 libcups2 libdrm2 libxkbcommon0 \
        libxcomposite1 libxdamage1 libxfixes3 libxrandr2 libgbm1 libpango-1.0-0 \
        libpangocairo-1.0-0 libasound2 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_sqlite pdo_mysql zip bcmath opcache \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Browsershot drives system Chromium via puppeteer-core, so the package needs
# to know where the binary lives. Setting it at the OS level means every PHP
# request and every node sub-process picks it up without per-call config.
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium
ENV PUPPETEER_SKIP_DOWNLOAD=true
# Override HOME at the image level so every supervisord program (queue,
# scheduler, php-fpm) resolves `os.homedir()` to a www-data-writable path
# when puppeteer-core does its config-dir statSync. Earlier attempts via
# supervisord `environment=` weren't enough — Docker ENV is inherited by
# ALL processes spawned from the image, including grandchild Node procs.
ENV HOME=/var/www/html
ENV XDG_CONFIG_HOME=/var/www/html/.config
ENV PUPPETEER_CACHE_DIR=/var/www/html/.cache/puppeteer

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

# Build the frontend, then drop dev-only npm packages but KEEP runtime ones
# (puppeteer-core for Browsershot). The old `rm -rf node_modules` would have
# broken PDF rendering on first request.
RUN npm run build && npm prune --omit=dev

RUN composer dump-autoload --optimize

# nginx + supervisord + php-fpm pool config
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/database \
    && mkdir -p /var/log/supervisor /run/nginx /run/php \
    # Defense in depth (3 layers) so puppeteer-core's statSync NEVER hits
    # EACCES again. Production diagnostic (PR #137) confirmed every brochure
    # render was failing with "permission denied, stat '/root/.config/
    # puppeteer'" because www-data could not even traverse /root (default
    # perms 700).
    #
    # 1. Create the new HOME-resolved puppeteer config dir (per ENV HOME above)
    #    with www-data ownership. statSync sees an existing readable dir.
    && mkdir -p /var/www/html/.config/puppeteer /var/www/html/.cache/puppeteer \
    && chown -R www-data:www-data /var/www/html/.config /var/www/html/.cache \
    && chmod -R 755 /var/www/html/.config /var/www/html/.cache \
    # 2. Open /root + /root/.config so even if HOME ever falls back to /root
    #    (legacy supervisord override, runtime override, etc.), the path is
    #    traversable + readable. No write access — just lookup.
    && chmod 755 /root \
    && mkdir -p /root/.config/puppeteer \
    && chmod -R 755 /root/.config

COPY docker-start.sh /var/www/html/start.sh
RUN chmod +x /var/www/html/start.sh

EXPOSE 8080

CMD ["/var/www/html/start.sh"]
