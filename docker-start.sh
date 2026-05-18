#!/bin/bash
set -e

# Railway provides PORT env var — default to 8080
PORT="${PORT:-8080}"

# Substitute PORT in nginx config (default site listens on 8080)
sed -i "s/listen 8080 default_server;/listen ${PORT} default_server;/" /etc/nginx/nginx.conf

# Ensure Laravel storage subdirectories exist (Railway mounts persistent volume here)
mkdir -p \
  /var/www/html/storage/app/public \
  /var/www/html/storage/framework/cache/data \
  /var/www/html/storage/framework/sessions \
  /var/www/html/storage/framework/testing \
  /var/www/html/storage/framework/views \
  /var/www/html/storage/logs \
  /var/www/html/storage/database
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chmod -R 775 /var/www/html/storage 2>/dev/null || true

# Persistent SQLite DB lives in the storage volume.
# First boot: migrate the ephemeral DB into the volume if a fresh volume.
PERSISTENT_DB=/var/www/html/storage/database/database.sqlite
EPHEMERAL_DB=/var/www/html/database/database.sqlite
if [ ! -f "$PERSISTENT_DB" ]; then
  if [ -f "$EPHEMERAL_DB" ] && [ -s "$EPHEMERAL_DB" ]; then
    cp "$EPHEMERAL_DB" "$PERSISTENT_DB"
  else
    touch "$PERSISTENT_DB"
  fi
fi
chmod 664 "$PERSISTENT_DB"
chown www-data:www-data "$PERSISTENT_DB" 2>/dev/null || true

# Create .env from example if not exists
if [ ! -f /var/www/html/.env ]; then
  cp /var/www/html/.env.example /var/www/html/.env
fi

# Override APP_URL with RAILWAY_PUBLIC_DOMAIN if available
if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
  sed -i "s|APP_URL=.*|APP_URL=https://${RAILWAY_PUBLIC_DOMAIN}|" /var/www/html/.env
fi

# Generate app key only if NOT provided via Railway env (APP_KEY)
# Otherwise sessions get invalidated on every deploy.
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force 2>/dev/null || true
fi

# Clear caches for fresh start
php artisan config:clear
php artisan cache:clear 2>/dev/null || true

# Run migrations and seed
php artisan migrate --force --seed

# Cache config and routes for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Recreate storage symlink (volume mount may have wiped it)
rm -f /var/www/html/public/storage
php artisan storage:link 2>/dev/null || true

# Final permission pass (volume mount may reset uid/gid)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "Starting nginx + php-fpm on port ${PORT}..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
