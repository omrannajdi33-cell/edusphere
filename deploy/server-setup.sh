#!/usr/bin/env bash
# À exécuter UNE FOIS sur le VPS (Ubuntu 22.04/24.04) en root ou sudo.
set -euo pipefail

APP_PATH="${1:-/var/www/edusphere}"
APP_USER="${2:-www-data}"

echo "→ Paquets système…"
apt-get update -qq
apt-get install -y -qq \
    nginx \
    mysql-server \
    git \
    unzip \
    curl \
    rsync \
    certbot \
    python3-certbot-nginx

echo "→ PHP 8.3…"
apt-get install -y -qq \
    php8.3-fpm php8.3-cli php8.3-mysql php8.3-sqlite3 \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath

echo "→ Composer…"
if ! command -v composer >/dev/null 2>&1; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "→ Node.js 22 (build secours)…"
if ! command -v node >/dev/null 2>&1; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt-get install -y -qq nodejs
fi

echo "→ Dossier application…"
mkdir -p "$APP_PATH"
chown -R "$APP_USER:$APP_USER" "$APP_PATH"

echo "→ MySQL — créer base (adapter le mot de passe) :"
echo "   CREATE DATABASE edusphere CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "   CREATE USER 'edusphere'@'localhost' IDENTIFIED BY 'MOT_DE_PASSE_FORT';"
echo "   GRANT ALL ON edusphere.* TO 'edusphere'@'localhost';"
echo "   FLUSH PRIVILEGES;"

echo ""
echo "✓ Serveur prêt. Ensuite :"
echo "  1. Copier deploy/nginx.edusphere.conf → /etc/nginx/sites-available/edusphere"
echo "  2. ln -s /etc/nginx/sites-available/edusphere /etc/nginx/sites-enabled/"
echo "  3. Créer $APP_PATH/.env (voir .env.example)"
echo "  4. Configurer les variables CI/CD GitLab et lancer deploy:production"
