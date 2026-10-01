#!/bin/sh
set -e

echo "→ Running database migrations..."
php artisan migrate --force

echo "→ Clearing and warming caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "→ Storage link..."
php artisan storage:link --quiet 2>/dev/null || true

echo "→ Syncing public assets to shared volume..."
cp -rT /public-seed/. /var/www/html/public/

echo "→ Laravel is ready. Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
