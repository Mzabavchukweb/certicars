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

# STORAGE PERSISTENCE — choose one option before going live:
#   Option A (recommended): Set FILESYSTEM_DISK=s3 with AWS_* env vars pointing at Cloudflare R2.
#                           All uploads go directly to R2 and survive redeploys automatically.
#   Option B: Mount a Railway Volume at /var/www/html/storage/app/public.
#             Uploads stay on the volume across redeploys.
# Without either option, uploaded images/videos are lost on every redeploy.

# Compiled views and file cache live in tmpfs (fast RAM disk), not NAS
mkdir -p /tmp/laravel-views /tmp/laravel-cache/data
chown -R www-data:www-data /tmp/laravel-views /tmp/laravel-cache
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

# Ensure R2 bucket allows cross-origin GET so Pannellum can load panoramas via XHR
php artisan r2:set-cors 2>&1 || true

# Storage symlink
rm -f /var/www/html/public/storage
php artisan storage:link 2>/dev/null || true

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Emergency admin password recovery.
# Set ADMIN_RECOVERY_HASH in Railway to a bcrypt hash of the desired password.
# This runs LAST — after migrations, seeders, and config cache — so it always wins.
# Remove ADMIN_RECOVERY_HASH after successful login to disable this hook.
if [ -n "$ADMIN_RECOVERY_HASH" ] && [ -n "$ADMIN_EMAIL" ]; then
    php -r "
        require '/var/www/html/vendor/autoload.php';
        \$app = require_once '/var/www/html/bootstrap/app.php';
        \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        \$updated = Illuminate\Support\Facades\DB::table('users')
            ->where('email', getenv('ADMIN_EMAIL'))
            ->update(['password' => getenv('ADMIN_RECOVERY_HASH'), 'is_admin' => 1, 'updated_at' => now()]);
        echo 'ADMIN_RECOVERY_HASH applied, rows updated: ' . \$updated . PHP_EOL;
    " 2>&1 || echo "ADMIN_RECOVERY_HASH apply failed"
fi

echo "Starting nginx + php-fpm on port ${PORT}..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
