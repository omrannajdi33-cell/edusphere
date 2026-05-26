#!/usr/bin/env bash
set -euo pipefail

# Script exécuté SUR LE SERVEUR après rsync GitLab CI.
# Prérequis serveur : PHP 8.3+, Composer, Node 20+, MySQL, Nginx/Apache

cd "$(dirname "$0")/.."

echo "→ Composer (production)…"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

if command -v npm >/dev/null 2>&1; then
  echo "→ Build assets (secours si artifacts absents)…"
  npm ci --omit=dev 2>/dev/null || npm ci
  npm run build
fi

echo "→ Laravel optimize…"
php artisan migrate --force --no-interaction
php artisan storage:link --force 2>/dev/null || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "→ Permissions storage…"
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "✓ Déploiement terminé."
