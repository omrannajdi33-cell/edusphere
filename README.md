# EduSphere

Plateforme scolaire Laravel pour école d'été (7–10 ans).

## Architecture

| Environnement | URL | Contenu |
|---------------|-----|---------|
| **Production (Render / VPS / Docker prod)** | `APP_URL` | Vitrine `/` + Laravel `/connexion`, `/eleve`, `/admin` — **même domaine** |
| **GitHub Pages** (optionnel) | https://omrannajdi33-cell.github.io/edusphere/ | Miroir vitrine → redirige vers `APP_URL/connexion` |
| **Local Docker** | http://localhost:8080 | Identique à la production |

## Production sur Render (recommandé)

1. [render.com](https://render.com) → **New** → **Blueprint** → repo GitHub `edusphere`
2. Le fichier `render.yaml` déploie Docker (nginx + PHP + PostgreSQL)
3. Attendre le déploiement → copier l'URL (ex. `https://edusphere-xxxx.onrender.com`)
4. Tester : `{URL}/` (vitrine) et `{URL}/connexion` (login)

Comptes démo après seed : `prof` / `prof123`, `ali` / `ali123`

### Lier GitHub Pages (miroir optionnel)

Une fois Render déployé :

```powershell
.\scripts\set-app-url.ps1 https://edusphere-xxxx.onrender.com
git add production-url
git push github main
```

Ou GitHub → Actions → **Publish GitHub Pages** → Run workflow avec `app_url`.

## Production sur VPS (GitLab CI)

1. Configurer `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_PATH`, `APP_URL`, `SSH_PRIVATE_KEY`
2. Nginx : `deploy/nginx.edusphere.conf` (vitrine + Laravel unifiés)
3. Push sur `main` → Play sur `deploy:production`

## Démarrage local avec Docker

Prérequis : [Docker Desktop](https://www.docker.com/products/docker-desktop/) ouvert.

```bash
cd edusphere
docker compose up --build          # dev (SQLite, hot reload volumes)
docker compose -f docker-compose.prod.yml up --build   # prod locale
```

| URL | Contenu |
|-----|---------|
| http://localhost:8080 | Vitrine |
| http://localhost:8080/connexion | Login Laravel |

Arrêter : `docker compose down`

## Fichiers clés

```
Dockerfile              → image production (Render / VPS Docker)
Dockerfile.dev          → image dev (docker-compose.yml)
docker-compose.yml      → dev local
docker-compose.prod.yml → test production local
render.yaml             → déploiement Render
index.html, connexion.html, assets/vitrine.js → vitrine (racine)
scripts/build-vitrine.sh → copie vitrine → public/vitrine ou Pages
production-url          → URL Laravel pour GitHub/GitLab Pages
```

## Git

- GitHub : https://github.com/omrannajdi33-cell/edusphere
- GitLab : https://gitlab.com/omrannajdi33-group/edusphere
