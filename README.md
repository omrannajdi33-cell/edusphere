# EduSphere

Plateforme scolaire Laravel pour école d'été (7–10 ans).

## Structure du projet

```
edusphere/
├── app/                 # Code Laravel
├── public/              # Racine web Laravel (index.php, build Vite)
├── site/vitrine/        # Vitrine statique GitLab Pages (≠ Laravel)
├── scripts/             # build-vitrine.sh (CI Pages)
├── deploy/              # Déploiement production
└── docs/                # Cahier des charges
```

| URL | Contenu |
|-----|---------|
| [gitlab.io/edusphere](https://omrannajdi33-group.gitlab.io/edusphere/) | Vitrine → redirige vers `/connexion` |
| `{APP_URL}/connexion` | Application Laravel (production) |

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

Ouvre http://127.0.0.1:8000/connexion

## GitLab

https://gitlab.com/omrannajdi33-group/edusphere

```bash
git remote add origin https://gitlab.com/omrannajdi33-group/edusphere.git
git push -u origin main
```

## GitHub

https://github.com/omrannajdi33-cell/edusphere

```bash
git remote add github https://github.com/omrannajdi33-cell/edusphere.git
git push -u github main
```

## Pipeline CI/CD (GitLab)

| Job | Rôle |
|-----|------|
| **test:php** | Tests PHPUnit |
| **build:assets** | Vite → `public/build/` |
| **pages** | Vitrine `site/vitrine/` → GitLab Pages |
| **deploy:production** | Laravel sur VPS (manuel) |

Variables : `APP_URL`, `DEPLOY_HOST`, `SSH_PRIVATE_KEY`, etc. — voir [deploy/DEPLOIEMENT.md](deploy/DEPLOIEMENT.md).

## Branche principale

`main`
