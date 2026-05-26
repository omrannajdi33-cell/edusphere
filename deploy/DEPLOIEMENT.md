# Déployer EduSphere avec GitLab CI/CD

## 1. Pipeline GitLab (automatique)

À chaque push sur `main` :

| Job | Rôle |
|-----|------|
| `test:php` | Tests PHPUnit + migrations SQLite |
| `build:assets` | `npm run build` → artifacts `public/build/` |
| `pages` | Publie la vitrine sur **GitLab Pages** (statique) |
| `deploy:production` | **Manuel** — rsync SSH + `deploy/deploy.sh` |

### GitLab Pages vs application Laravel

| URL | Contenu |
|-----|---------|
| https://omrannajdi33-group.gitlab.io/edusphere/ | Vitrine statique (job `pages`) — **pas** l'app Laravel |
| Ton `APP_URL` (VPS / hébergeur PHP) | Application complète (connexion, élèves, exercices) |

GitLab Pages ne peut exécuter que du HTML/CSS/JS. Laravel exige PHP + MySQL sur un vrai serveur.

**Obligatoire pour le bouton Connexion sur la vitrine :**

1. GitLab → **Settings → CI/CD → Variables**
2. Ajouter **`APP_URL`** = `https://ton-domaine-de-production.com` (HTTPS, sans slash final)
3. Relancer le pipeline (ou push sur `main`)

Le job `pages` génère alors le lien `{APP_URL}/connexion`.

Alternatives : variable `PAGES_APP_URL`, ou fichier `site/vitrine/production-url` (une ligne, URL HTTPS).
Si `DEPLOY_HOST` est déjà défini (sans `APP_URL`), l’URL est déduite automatiquement.

## 2. Variables GitLab (Settings → CI/CD → Variables)

| Variable | Type | Exemple |
|----------|------|---------|
| `SSH_PRIVATE_KEY` | Masked | Contenu de la clé privée SSH |
| `DEPLOY_HOST` | Variable | `123.45.67.89` ou `edusphere.mondomaine.com` |
| `DEPLOY_USER` | Variable | `deploy` ou `root` |
| `DEPLOY_PATH` | Variable | `/var/www/edusphere` |
| `APP_URL` | Variable | `https://edusphere.mondomaine.com` (affichée dans GitLab Environments) |

### Clé SSH pour GitLab

Sur votre PC :

```bash
ssh-keygen -t ed25519 -C "gitlab-edusphere" -f ~/.ssh/edusphere_deploy
```

- Coller le contenu de `edusphere_deploy` (privée) dans `SSH_PRIVATE_KEY`
- Coller `edusphere_deploy.pub` dans `~/.ssh/authorized_keys` sur le serveur

## 3. Préparer le serveur (une fois)

```bash
# Sur le VPS Ubuntu
sudo bash deploy/server-setup.sh /var/www/edusphere www-data
```

Puis :

```bash
sudo cp deploy/nginx.edusphere.conf /etc/nginx/sites-available/edusphere
# Éditer YOUR_DOMAIN et les chemins SSL
sudo ln -sf /etc/nginx/sites-available/edusphere /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### Base MySQL

```sql
CREATE DATABASE edusphere CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'edusphere'@'localhost' IDENTIFIED BY 'VOTRE_MOT_DE_PASSE';
GRANT ALL ON edusphere.* TO 'edusphere'@'localhost';
FLUSH PRIVILEGES;
```

### Fichier `.env` sur le serveur

```bash
cd /var/www/edusphere
cp .env.example .env
nano .env
php artisan key:generate
```

Minimum à configurer :

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=edusphere
DB_USERNAME=edusphere
DB_PASSWORD=...

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Premier déploiement manuel (optionnel) :

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

Comptes démo après seed : `prof` / `prof123`, `ali` / `ali123`

## 4. Lancer un déploiement

1. Push sur `main` (voir ci-dessous)
2. GitLab → **Build → Pipelines**
3. Attendre `test:php` et `build:assets` (verts)
4. Cliquer **Play** sur `deploy:production`

## 5. HTTPS (Let's Encrypt)

```bash
sudo certbot --nginx -d votre-domaine.com
```

## 6. Dépannage

| Problème | Solution |
|----------|----------|
| 500 après deploy | `storage/logs/laravel.log`, vérifier `.env` et `APP_KEY` |
| CSS/JS cassés | Vérifier `public/build/manifest.json` existe |
| Permission denied storage | `chmod -R ug+rwx storage bootstrap/cache` |
| Pipeline test échoue | Voir job log ; migrations SQLite en CI |
