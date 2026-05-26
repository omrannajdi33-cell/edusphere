# EduSphere

Plateforme scolaire Laravel pour école d'été (7–10 ans).

## Démarrage local (Laragon)

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
php artisan storage:link
php artisan serve
```

Comptes démo : `prof` / `prof123` — `ali` / `ali123`

## GitLab

Dépôt : [gitlab.com/omrannajdi33-group/edusphere](https://gitlab.com/omrannajdi33-group/edusphere.git)

```bash
git clone https://gitlab.com/omrannajdi33-group/edusphere.git
cd edusphere
```

Remote déjà configuré si tu as cloné depuis GitLab. Sinon :

```bash
git remote add origin https://gitlab.com/omrannajdi33-group/edusphere.git
git push -u origin main
```

## Pipeline CI/CD (GitLab)

Le fichier `.gitlab-ci.yml` définit 3 étapes :

| Étape | Rôle |
|-------|------|
| **test** | `composer install` + `php artisan test` |
| **build** | `npm ci` + `npm run build` (assets Vite) |
| **deploy** | Manuel — envoie le code sur ton serveur via SSH |

### Variables CI/CD à ajouter

GitLab → **Settings → CI/CD → Variables** :

| Variable | Exemple | Protégée |
|----------|---------|----------|
| `SSH_PRIVATE_KEY` | contenu de ta clé privée SSH | ✓ masked |
| `DEPLOY_HOST` | `123.45.67.89` | ✓ |
| `DEPLOY_USER` | `deploy` | ✓ |
| `DEPLOY_PATH` | `/var/www/edusphere` | ✓ |
| `APP_URL` | `https://ton-domaine.com` | |

### Guide complet de mise en ligne

Voir **[deploy/DEPLOIEMENT.md](deploy/DEPLOIEMENT.md)** — variables GitLab, Nginx, MySQL, `.env`, certbot.

Fichiers utiles :

| Fichier | Rôle |
|---------|------|
| `.gitlab-ci.yml` | Pipeline test → build → deploy |
| `deploy/deploy.sh` | Script exécuté sur le serveur |
| `deploy/server-setup.sh` | Installation initiale du VPS |
| `deploy/nginx.edusphere.conf` | Exemple Nginx |

## Branche principale

`main` — les merge requests déclenchent test + build ; le deploy ne part que depuis `main`.
