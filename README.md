# EduSphere

Plateforme scolaire Laravel pour école d'été (7–10 ans).

## Site vitrine (GitHub Pages)

Fichiers à la **racine du repo** :

```
index.html          ← page d'accueil
connexion/index.html
404.html
manifest.json
production-url        ← URL Laravel quand l'app sera en ligne
```

**URL :** https://omrannajdi33-cell.github.io/edusphere/

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

Connexion locale : http://127.0.0.1:8000/connexion

## Git

- GitLab : https://gitlab.com/omrannajdi33-group/edusphere
- GitHub : https://github.com/omrannajdi33-cell/edusphere

## Branche principale

`main`
