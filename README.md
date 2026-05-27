# EduSphere

Plateforme scolaire Laravel pour école d'été (7–10 ans).

## Architecture production

| Composant | URL | Rôle |
|-----------|-----|------|
| **Vitrine** | https://omrannajdi33-cell.github.io/edusphere/ | Pages HTML (GitHub Pages) |
| **Application** | `APP_URL` (Render / VPS) | Laravel : connexion, élèves, exercices |

GitHub Pages **ne peut pas** exécuter PHP. L'app Laravel doit être hébergée séparément.

## Mise en production (2 étapes)

### 1. Déployer Laravel sur Render

1. [render.com](https://render.com) → **New** → **Blueprint** → repo GitHub `edusphere`
2. Le fichier `render.yaml` crée l'app + PostgreSQL
3. Attendre le déploiement → copier l'URL (ex. `https://edusphere-xxxx.onrender.com`)
4. Tester : `{URL}/connexion`

### 2. Lier la vitrine GitHub

1. GitHub → repo → **Settings** → **Secrets and variables** → **Actions** → **Variables**
2. Ajouter **`APP_URL`** = `https://edusphere-xxxx.onrender.com` (sans slash final)
3. **Actions** → **Publish GitHub Pages** → **Run workflow**

La vitrine redirigera alors vers `{APP_URL}/connexion` (plus de localhost).

## Fichiers vitrine (racine)

```
index.html
connexion.html    → redirige vers APP_URL/connexion en production
404.html
assets/edu.css
production-url
```

## Démarrage local (dev uniquement)

```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Connexion locale : http://127.0.0.1:8000/connexion

## Git

- GitHub : https://github.com/omrannajdi33-cell/edusphere
- GitLab : https://gitlab.com/omrannajdi33-group/edusphere
