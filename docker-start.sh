#!/bin/bash
set -e

# Railway provides PORT env var — default to 8080
PORT="${PORT:-8080}"

# Ensure Laravel storage subdirectories exist (Railway mounts persistent volume here)
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

# Generate app key if not set
php artisan key:generate --force 2>/dev/null || true

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

echo "Starting Laravel server on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
