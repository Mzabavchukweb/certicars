#!/bin/bash
set -e

# Railway provides PORT env var — default to 80
PORT="${PORT:-80}"

# Configure Apache to listen on the correct port
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf

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

# Create storage link (ignore if exists)
php artisan storage:link 2>/dev/null || true

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground
