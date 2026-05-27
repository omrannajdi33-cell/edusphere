#!/bin/sh
set -e

cd /var/www

export PORT="${PORT:-8080}"

if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
  APP_URL="${RENDER_EXTERNAL_URL}"
fi

APP_URL="${APP_URL:-http://localhost:${PORT}}"
case "$APP_URL" in
  http://*|https://*) ;;
  *) APP_URL="https://${APP_URL}" ;;
esac
export APP_URL

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database
chmod -R ug+rwx storage bootstrap/cache database 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache database 2>/dev/null || true

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ -n "${APP_KEY:-}" ]; then
  grep -q '^APP_KEY=' .env && sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env || echo "APP_KEY=${APP_KEY}" >> .env
fi

if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
  php artisan key:generate --force --no-interaction
fi

php artisan migrate --force --no-interaction

if [ "${SEED_DEMO:-false}" = "true" ]; then
  php artisan db:seed --force --no-interaction || true
fi

php artisan storage:link --force 2>/dev/null || true
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction

VITRINE_OUT=public/vitrine APP_URL="${APP_URL}" bash scripts/build-vitrine.sh

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

echo "EduSphere production : ${APP_URL}"

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
