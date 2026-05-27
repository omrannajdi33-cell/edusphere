#!/bin/sh
set -e

cd /var/www

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

if [ ! -d node_modules/vite ]; then
  npm ci
fi

if [ ! -f .env ]; then
  cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
  php artisan key:generate --force --no-interaction
fi

if [ ! -f public/build/manifest.json ]; then
  npm run build
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
touch database/database.sqlite
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction || true
php artisan storage:link --force 2>/dev/null || true

APP_URL=http://localhost:8080 VITRINE_OUT=public/vitrine bash scripts/build-vitrine.sh

exec "$@"
