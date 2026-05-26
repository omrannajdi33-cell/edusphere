# Site vitrine (GitLab Pages)

Fichiers statiques publiés sur **https://omrannajdi33-group.gitlab.io/edusphere/**

| Fichier | Rôle |
|---------|------|
| `index.html` | Redirige immédiatement vers la connexion |
| `presentation.html` | Page de présentation du projet (optionnelle) |
| `connexion/index.html` | Redirige vers l'app Laravel en production |
| `connexion/pending.html` | Affiché si `APP_URL` n'est pas encore configurée |
| `404.html` | Page introuvable sur la vitrine |
| `production-url` | URL prod alternative (une ligne, ou via CI `APP_URL`) |

L'application Laravel (connexion, élèves, exercices) vit dans **`public/`** à la racine du projet et s'exécute sur un **serveur PHP** (`APP_URL`), pas sur GitLab Pages.

Le job CI `pages` assemble `site/vitrine/` → artefact `public/` via `scripts/build-vitrine.sh`.
