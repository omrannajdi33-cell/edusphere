#!/usr/bin/env bash
set -euo pipefail

# Exécuté SUR LE SERVEUR après rsync GitLab CI.
# Prérequis : PHP 8.3+, Composer, MySQL, .env déjà présent sur le serveur

cd "$(dirname "$0")/.."

if [[ ! -f .env ]]; then
    echo "ERREUR: fichier .env manquant sur le serveur."
    echo "Copiez .env.example vers .env et configurez APP_KEY, DB_*, APP_URL."
    exit 1
fi

echo "→ Composer (production)…"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

if [[ ! -f public/build/manifest.json ]]; then
    echo "→ Build Vite (artifacts CI absents)…"
    if command -v npm >/dev/null 2>&1; then
        npm ci --omit=dev 2>/dev/null || npm ci
        npm run build
    else
        echo "ERREUR: public/build/manifest.json absent et npm non installé."
        exit 1
    fi
else
    echo "→ Assets CI détectés (public/build/manifest.json)."
fi

echo "→ Migrations…"
php artisan migrate --force --no-interaction

echo "→ Liens & cache…"
php artisan storage:link --force 2>/dev/null || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "→ Permissions…"
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "✓ Déploiement terminé — $(date -Iseconds)"
