#!/bin/bash
set -e

PORT="${PORT:-8080}"

sed -i "s/listen 8080 default_server;/listen ${PORT} default_server;/" /etc/nginx/nginx.conf

# Ensure Laravel storage subdirectories exist
mkdir -p \
  /var/www/html/storage/app/public \
  /var/www/html/storage/framework/cache/data \
  /var/www/html/storage/framework/sessions \
  /var/www/html/storage/framework/testing \
  /var/www/html/storage/framework/views \
  /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chmod -R 775 /var/www/html/storage 2>/dev/null || true

# Create .env from example if not exists
if [ ! -f /var/www/html/.env ]; then
  cp /var/www/html/.env.example /var/www/html/.env
fi

# Override APP_URL with RAILWAY_PUBLIC_DOMAIN if available
if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
  sed -i "s|APP_URL=.*|APP_URL=https://${RAILWAY_PUBLIC_DOMAIN}|" /var/www/html/.env
fi

# Auto-configure MySQL from Railway environment variables
if [ -n "$MYSQL_HOST" ]; then
  sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mysql|" /var/www/html/.env
  grep -q "^DB_HOST=" /var/www/html/.env \
    && sed -i "s|^DB_HOST=.*|DB_HOST=${MYSQL_HOST}|" /var/www/html/.env \
    || echo "DB_HOST=${MYSQL_HOST}" >> /var/www/html/.env
  grep -q "^DB_PORT=" /var/www/html/.env \
    && sed -i "s|^DB_PORT=.*|DB_PORT=${MYSQL_PORT:-3306}|" /var/www/html/.env \
    || echo "DB_PORT=${MYSQL_PORT:-3306}" >> /var/www/html/.env
  grep -q "^DB_DATABASE=" /var/www/html/.env \
    && sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${MYSQL_DATABASE}|" /var/www/html/.env \
    || echo "DB_DATABASE=${MYSQL_DATABASE}" >> /var/www/html/.env
  grep -q "^DB_USERNAME=" /var/www/html/.env \
    && sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${MYSQL_USER}|" /var/www/html/.env \
    || echo "DB_USERNAME=${MYSQL_USER}" >> /var/www/html/.env
  grep -q "^DB_PASSWORD=" /var/www/html/.env \
    && sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${MYSQL_PASSWORD}|" /var/www/html/.env \
    || echo "DB_PASSWORD=${MYSQL_PASSWORD}" >> /var/www/html/.env
  echo "Using MySQL: ${MYSQL_HOST}/${MYSQL_DATABASE}"
fi

# Generate app key only if not provided via env
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force 2>/dev/null || true
fi

php artisan config:clear
php artisan cache:clear 2>/dev/null || true

php artisan migrate --force

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage symlink
rm -f /var/www/html/public/storage
php artisan storage:link 2>/dev/null || true

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "Starting nginx + php-fpm on port ${PORT}..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
