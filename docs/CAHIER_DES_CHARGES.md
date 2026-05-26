# EDUSPHERE — Cahier des charges ultime + TODO LIST

> **Version** : 3.0 — document de référence unique  
> **Public** : école d’été personnalisée, enfants 7–10 ans, 1 prof/admin, plusieurs élèves  
> **Philosophie** : **TABLETTE FIRST** (iPad / tablette Android), puis ordinateur  
> **Contenu** : 100 % créé manuellement par l’admin — **aucune IA**

---

## Liens GitLab (important)

| Ressource | URL |
|-----------|-----|
| **Dépôt code** | https://gitlab.com/omrannajdi33-group/edusphere |
| **Pipelines CI/CD** | https://gitlab.com/omrannajdi33-group/edusphere/-/pipelines |
| **Guide déploiement** | [deploy/DEPLOIEMENT.md](../deploy/DEPLOIEMENT.md) |

### Pourquoi GitLab Pages affiche 404

`https://omrannajdi33-group.gitlab.io/edusphere/` ne peut **pas** héberger EduSphere tel quel.

GitLab Pages sert du **HTML/CSS/JS statique** uniquement. EduSphere exige :

- PHP (Laravel)
- MySQL (ou SQLite en local)
- Sessions, authentification, uploads, API interne

**Workflow correct :**

```
Développement local (Laragon / artisan serve)
        ↓
Versioning + CI (GitLab)
        ↓
Déploiement réel (VPS via deploy:production, Railway, Render, Hostinger…)
```

Le fichier `.gitlab-ci.yml` sert à **tester, builder les assets et déployer sur un vrai serveur**, pas à publier Laravel sur Pages.

---

## 1. Vision du projet

EduSphere n’est **pas** un clone de Google Classroom avec des « cours » à ajouter/supprimer.

C’est une **plateforme edtech modulaire** qui fusionne les idées de :

- **ClassDojo** (points, grille élèves, feedback visible)
- **Goodnotes / Notability** (pages interactives, dessin, surlignage, correction à l’encre)
- **Khan Academy / Duolingo** (progression par compétence, interfaces spécialisées)

### Principe architectural central

```
Matière
 └── Compétence (module spécialisé)
      └── Activité (exercice OU examen)
           └── Pages interactives internes + questions adaptées
```

Chaque **compétence** possède :

- sa propre interface (pas un formulaire générique)
- ses propres outils (canvas, audio, carte, checklist…)
- sa propre logique pédagogique
- ses propres types de questions pertinents

---

## 2. Technologies imposées

| Couche | Stack |
|--------|--------|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Base de données | MySQL (production) · SQLite (dev local) |
| Local | Laragon ou `php artisan serve` |
| Versioning / CI | GitLab (`.gitlab-ci.yml`) |
| PWA | `manifest.json`, mode standalone, icône, splash |

---

## 3. Rôles utilisateurs

### Admin / Professeur (rôle unique)

Accès complet : élèves, matières, compétences, activités, correction, points, bulletins, calendrier, annonces.

### Élève

Accès limité : matières assignées, activités/examens de son niveau, lecture, travail interactif, résultats, points, annonces.

**Restrictions obligatoires :**

- impossible d’accéder à `/admin`
- impossible de modifier ses notes
- impossible de voir le travail des autres élèves

---

## 4. Design global (TABLETTE FIRST)

### Objectif visuel

Design **premium, ultra moderne, minimaliste, tactile**, inspiré de Apple, Linear, Notion, ClassDojo, Goodnotes.

### Exigences UI

- cartes flottantes, coins très arrondis (XL), ombres douces
- glassmorphism léger, animations fluides, transitions premium
- **gros boutons**, navigation simple, lisible pour enfants 7–10 ans
- **plein écran** sur activités, lecture et examens (pas de chrome inutile)
- sur iPad : mode standalone PWA, support stylet (Apple Pencil) et doigt
- couleurs par matière cohérentes partout (dashboard, cartes, barres d’état)

### Palette matières (cible)

| Matière | Couleur |
|---------|---------|
| Français | Bleu |
| Mathématiques | Violet |
| Sciences | Vert |
| Histoire | Orange |
| Géographie | Cyan |
| Islam | Emerald |
| Natation | Aqua |
| Éducation physique | Rouge doux |

> **État actuel** : refonte CSS en cours (`tokens.css`, `edu-base.css`) — palette et dashboards à finaliser.

---

## 5. Niveaux scolaires & assignation

Chaque élève possède :

- **niveau scolaire** (ex. 2e, 3e année — enum `StudentLevel`)
- avatar, points cumulés, progression

Lors de la création d’une activité, l’admin choisit :

1. **niveau cible**
2. **élèves assignés** (relation `activity_student`)
3. **type** : exercice **ou** examen
4. **mode correction** : automatique **ou** manuelle

L’élève ne voit **que** les activités qui lui sont assignées et qui correspondent à son niveau.

---

## 6. Matières officielles (programme québécois primaire)

Pas de système « ajouter un cours ». Les matières sont **prédéfinies** avec leurs compétences, alignées sur le programme québécois (français langue d’enseignement, mathématique, univers social, science et technologie, éducation à la vie affective / valeurs pour l’Islam, etc.).

Références utiles :

- [Programme de formation de l’école québécoise — primaire](https://www.quebec.ca/education/programmes-formation/ecole-quebecoise/primaire)
- Domaines français : **lire**, **écrire**, **communiquer oralement**
- Mathématique : **résoudre une situation-problème**, **développer et déployer des outils mathématiques**, **déployer des raisonnements**
- Science : **Univers vivant**, **Univers matériel**, **Terre et espace**, **Techniques et technologies**
- Univers social : **Temps**, **Espace**, **Société**

### Liste des matières EduSphere

1. **Français**
2. **Mathématiques**
3. **Sciences**
4. **Éducation physique**
5. **Natation** *(matière spéciale à part entière)*
6. **Histoire**
7. **Géographie**
8. **Islam**

Chaque matière a : couleur, icône, compétences, modules UI dédiés.

---

## 7. Compétences & modules par matière

> Chaque compétence avec `module_type` possède une vue Blade + JS Alpine dédiés dans `resources/views/modules/{type}/` et `resources/js/modules/`.

### 🇫🇷 Français

| Compétence | Module | Interface spécialisée |
|------------|--------|------------------------|
| Lecture / compréhension | `lecture` | Texte plein écran, surlignage, zoom, retour lecture ↔ exercice |
| Écriture | `ecriture` | Grande zone type Goodnotes/Docs : gras, italique, surlignage, compteur mots |
| Communication orale | `oral` | Enregistrement audio, minuterie, réécoute, dépôt |
| Vocabulaire | *(générique)* | À spécialiser |
| Orthographe | *(générique)* | À spécialiser |
| Grammaire | *(générique)* | À spécialiser |

### ➗ Mathématiques

| Compétence | Module | Interface spécialisée |
|------------|--------|------------------------|
| Arithmétique | `calcul` | Clavier math, réponses étape par étape |
| Résolution de problèmes | `problemes` | Énoncé + **brouillon plein écran** (dessin, surligneur, réponse en popup) |
| Géométrie | `geometrie` | Tracer lignes, formes, glisser-déposer |
| Mesures, Fractions, Logique… | *(générique)* | Modules dédiés à créer |

### 🔬 Sciences

| Compétence | Module | Interface spécialisée |
|------------|--------|------------------------|
| Observation | `observation` | Images annotables, légendes, identification |
| Expérimentation | `experience` | Étapes, tableaux d’observations, réponses ouvertes |
| Univers vivant / matériel / Terre | *(générique)* | À spécialiser |

### 🌍 Histoire & Géographie

| Compétence | Module | Interface spécialisée |
|------------|--------|------------------------|
| Temps historique | `timeline` | Frise, glisser-déposer, ordre chronologique |
| Cartes / Territoires | `carte` | Carte interactive, placer/relier éléments |

### ☪️ Islam

| Compétence | Module | Interface spécialisée |
|------------|--------|------------------------|
| Lecture | `lecture_islamique` | Texte arabe, audio, répétition |
| Histoire islamique | `histoire_islamique` | Histoires, quiz, chronologie |

### 🏃 Éducation physique

| Compétence | Module | Interface spécialisée |
|------------|--------|------------------------|
| Coordination / activité | `activite` | Checklist, minuterie, validation prof |

### 🏊 Natation

| Compétence | Module | Interface spécialisée |
|------------|--------|------------------------|
| Flottaison / techniques | `techniques` | Vidéos, checklist progression, validation manuelle |
| Sécurité aquatique | `securite` | Scénarios, quiz sécurité, images interactives |

---

## 8. Lecture interactive (OBLIGATOIRE)

Avant certains exercices/examens, l’élève **doit lire un texte**.

### Contenu du texte (admin)

- saisie manuelle dans l’admin **OU**
- import de contenu riche (texte structuré — **pas de dépendance PDF** pour le flux principal)

### Mode lecture plein écran

- le texte **occupe toute la page**
- lisible tablette, zoom, surlignement
- lecture ligne par ligne (optionnel)
- sauvegarde de la position de lecture
- **consultable à tout moment** pendant l’exercice ou l’examen

### Navigation

- bouton **« Retour exercice »**
- bouton **« Retour lecture »**
- transitions fluides, sans perdre le travail en cours

---

## 9. Moteur de pages interactives (CŒUR DU PROJET)

### ❌ Ce qu’on ne veut PLUS

- Importer des PDF comme support principal d’activité
- Extensions/plugins PDF externes pour l’écriture

### ✅ Ce qu’on veut

**Moteur interne propriétaire** — chaque activité = suite de **pages numériques** stockées en base.

#### Fonctionnalités élève (plein écran)

- écrire (stylet / doigt)
- dessiner (stylo, surligneur, gomme)
- surligner
- effacer (partiel / page entière)
- **changer de page** (pagination claire)
- **sauvegarde automatique** (autosave)
- reprendre plus tard
- soumettre

#### Fonctionnalités professeur

- voir le travail soumis page par page
- **corriger à l’encre** (annotations par-dessus le travail élève)
- commenter, noter
- **recorriger** et **renvoyer** pour que l’élève refasse ses erreurs
- conserver **historique des versions** (soumissions + corrections)

#### Workflow correction

```
1. Élève soumet
2. Prof corrige à l’encre + note + commentaires
3. Prof renvoie (statut « à refaire » ou équivalent)
4. Élève corrige et resoumet
5. Prof recorrige
→ Tout reste visible pour l’élève (résultat, annotations, historique)
```

> **État actuel** : canvas partiel sur module `problemes` et feuilles réponses ; moteur multi-pages unifié et correction à l’encre admin **à construire**.

---

## 10. Activités : exercice vs examen

À la création, l’admin choisit **`purpose`** :

| | Exercice | Examen |
|---|----------|--------|
| Barre visuelle | Bleue « Exercice » | Rouge « Examen » |
| Minuterie | Optionnelle | Obligatoire (`exam_duration_minutes`) |
| Tentatives | Plus souple | **Une seule tentative** (cible) |
| Auto-soumission | Non | **Oui** à expiration du timer (cible) |
| Comptabilisation bulletin | Non | Oui (`counts_for_bulletin`) |
| Correction | Auto ou manuelle (`grading_mode`) | Souvent manuelle |
| Grille correction manuelle | Genre, effort, compréhension, expression, orthographe, progression | Idem |

---

## 11. Types de questions obligatoires (15)

Le moteur dynamique doit supporter :

1. Choix multiples (QCM)
2. Vrai / Faux
3. Réponse courte
4. Réponse longue
5. Relier les éléments
6. Mettre dans l’ordre
7. Compléter les trous
8. Sélection multiple
9. Associer image et réponse
10. Calcul numérique
11. Glisser-déposer
12. Identifier sur image
13. Question audio
14. Tableau à compléter
15. Cases à cocher

> **État actuel** : enum `QuestionType` + rendu partiel dans `_question.blade.php` — tous les types interactifs ne sont pas encore implémentés.

---

## 12. Système de points (ClassDojo)

### Interface admin

- élèves affichés en **GRILLE** (cartes), pas en liste
- chaque carte : avatar, nom, total points
- clic sur un élève → **popup** avec :
  - section **Positif** (participation, effort, respect, excellent travail…)
  - section **Négatif** (distraction, retard, manque d’effort…)
- chaque action configurable : **nom + valeur points** (`point_behaviors`)

### Interface élève

- voir total, historique, **pourquoi** les points ont été donnés/retirés
- voir classement (optionnel)
- voir sa progression

> **État actuel** : CRUD points + behaviors en base ; **grille ClassDojo + popup** à refaire.

---

## 13. Résultats, bulletins & visibilité élève

L’élève **doit** pouvoir voir :

- ses notes et résultats d’examens
- les corrections et annotations du prof
- les commentaires
- sa progression par matière

Le système admin doit générer :

- moyenne générale
- moyenne par matière
- statistiques, progression, bulletins PDF/HTML (cible)

> **État actuel** : pages résultat activité + bulletins admin basiques ; affichage annotations et PDF bulletins **incomplet**.

---

## 14. Organisation scolaire

- horaire hebdomadaire
- calendrier (événements, examens, devoirs)
- annonces (publiées, modifiables, suppressibles)
- rappels importants sur dashboard élève

> **État actuel** : modèles `CalendarEvent`, `Announcement` + affichage partiel dashboard.

---

## 15. PWA / Application tablette

| Exigence | Détail |
|----------|--------|
| manifest.json | Nom, icônes, `display: standalone` |
| Splash screen | Au lancement depuis écran d’accueil |
| Plein écran | Masquer UI navigateur |
| Tactile | Zones touch ≥ 44px, gestes simples |
| Offline partiel | Cache assets statiques (cible) |
| iPad | `viewport-fit=cover`, safe areas |

Installation : Chrome → **Ajouter à l’écran d’accueil**.

---

## 16. Sécurité

- routes admin protégées (`middleware admin`)
- routes élève protégées (`middleware student`)
- mots de passe hashés (bcrypt)
- validation Laravel côté serveur
- CSRF sur tous les formulaires
- examens : verrouillage après soumission / expiration
- sauvegarde automatique du travail élève (anti-perte)

---

## 17. État d’avancement du code (résumé)

| Domaine | Statut |
|---------|--------|
| Auth login/logout | ✅ Fait |
| CRUD élèves admin | ✅ Fait |
| 8 matières + compétences seedées | ✅ Fait |
| 15 modules compétence (vues de base) | ✅ Partiel |
| Activité exercice/examen + timer | ✅ Partiel (auto-submit timer à finir) |
| Correction auto/manuelle + grille 6 critères | ✅ Partiel |
| Texte lecture + lien activité | ✅ Partiel (plein écran à peaufiner) |
| Canvas brouillon (problèmes) | ✅ Partiel |
| Moteur pages multi-pages interne | ❌ À faire |
| Correction à l’encre + renvoi + historique | ❌ À faire |
| 15 types de questions interactifs | ❌ Partiel |
| Points grille ClassDojo | ❌ À faire |
| Bulletins complets | ❌ Partiel |
| Design ultra moderne tablette | ❌ En cours |
| PWA complète | ❌ Partiel (`manifest.json` existe) |
| CI/CD GitLab test + build + deploy SSH | ✅ Fait |
| GitLab Pages | ❌ Non applicable (Laravel) |

---

# TODO LIST — ADMIN / PROFESSEUR

> Légende : ✅ fait · 🔶 partiel · ⬜ à faire

## 👤 Comptes

| Statut | Tâche |
|--------|-------|
| ✅ | Se connecter |
| ✅ | Se déconnecter |
| ⬜ | Modifier profil admin |
| ⬜ | Changer mot de passe |

## 👨‍🎓 Élèves

| Statut | Tâche |
|--------|-------|
| ✅ | Ajouter élève |
| ✅ | Modifier élève |
| ✅ | Supprimer élève |
| ✅ | Réinitialiser mot de passe |
| 🔶 | Ajouter avatar (champ existe, UI à enrichir) |
| 🔶 | Définir niveau scolaire par élève |
| ⬜ | Voir profil élève détaillé |
| ⬜ | Voir progression / statistiques élève |

## 📚 Matières & compétences

| Statut | Tâche |
|--------|-------|
| ✅ | Matières prédéfinies (8 matières) |
| ✅ | Compétences par matière (seed Québec) |
| ✅ | Couleurs et icônes matières |
| ⬜ | Admin : éditer compétences sans code |
| ⬜ | Modules UI pour compétences sans `module_type` |

## 📖 Lecture interactive

| Statut | Tâche |
|--------|-------|
| ✅ | Saisir texte lecture dans activité |
| 🔶 | Import contenu riche (non-PDF) |
| 🔶 | Mode lecture plein écran |
| ✅ | Relier texte ↔ exercice/examen |
| ⬜ | Zoom + surlignement lecture |
| ⬜ | Lecture ligne par ligne |
| ⬜ | Sauvegarde position lecture |
| ✅ | Boutons retour lecture / retour exercice |

## 📝 Moteur pages interactives

| Statut | Tâche |
|--------|-------|
| ⬜ | Créer activité multi-pages interne |
| ⬜ | Navigation pages (suivante / précédente) |
| ⬜ | Écrire + dessiner + surligner + effacer |
| ⬜ | Sauvegarde automatique par page |
| ⬜ | Plein écran obligatoire |
| ⬜ | Soumettre activité complète |
| ⬜ | Voir soumission page par page (admin) |
| ⬜ | Corriger à l’encre |
| ⬜ | Recorriger + renvoyer à l’élève |
| ⬜ | Historique versions |

## ❓ Questions dynamiques

| Statut | Tâche |
|--------|-------|
| ✅ | Créer activité avec sections/questions |
| 🔶 | 15 types de questions (rendu partiel) |
| ⬜ | Images dans questions |
| ⬜ | Audio dans questions |
| ⬜ | Pages / étapes / sections avancées |
| ✅ | Sauvegarde automatique réponses (progression) |
| ✅ | Correction automatique (QCM etc.) |
| ✅ | Correction manuelle + grille 6 critères |

## 📋 Examens

| Statut | Tâche |
|--------|-------|
| ✅ | Choisir type examen vs exercice |
| ✅ | Minuterie examen (affichage) |
| ⬜ | Auto-soumission fin du temps |
| ⬜ | Une seule tentative stricte |
| ⬜ | Date/heure ouverture / fermeture |
| ⬜ | Lancer / fermer examen manuellement |
| ✅ | Voir examens soumis (corrections) |
| 🔶 | Publier résultats visibles élève |

## ⭐ Points (ClassDojo)

| Statut | Tâche |
|--------|-------|
| ✅ | Ajouter / retirer points |
| ✅ | Actions positives/négatives configurables |
| ⬜ | Grille élèves (cartes) |
| ⬜ | Popup points au clic |
| ⬜ | Classement élèves |

## 📊 Bulletins & résultats

| Statut | Tâche |
|--------|-------|
| 🔶 | Page bulletins admin |
| ⬜ | Moyenne générale |
| ⬜ | Moyenne par matière |
| ⬜ | Export bulletin PDF |
| ⬜ | Progression détaillée |

## 📅 Organisation

| Statut | Tâche |
|--------|-------|
| 🔶 | Calendrier / événements (modèle + affichage) |
| ⬜ | Horaire hebdomadaire éditable |
| ⬜ | Devoirs sur calendrier |
| ⬜ | Examens sur calendrier |

## 📢 Communication

| Statut | Tâche |
|--------|-------|
| ✅ | Publier annonces |
| ✅ | Modifier / supprimer annonces |
| ⬜ | Rappels importants dashboard |

## 📱 PWA & tablette

| Statut | Tâche |
|--------|-------|
| 🔶 | manifest.json |
| ⬜ | Splash screen complet |
| ⬜ | Icônes toutes tailles |
| ⬜ | Mode standalone testé iPad |
| ⬜ | Support Apple Pencil optimisé |

## 🎨 UI / UX

| Statut | Tâche |
|--------|-------|
| 🔶 | Dashboard admin moderne |
| 🔶 | Dashboard élève moderne |
| ⬜ | Refonte design ultra premium |
| ⬜ | Animations fluides globales |
| ⬜ | Palette matières finalisée |

---

# TODO LIST — ÉLÈVE

## 🔐 Compte

| Statut | Tâche |
|--------|-------|
| ✅ | Se connecter |
| ✅ | Se déconnecter |
| 🔶 | Voir profil / avatar |
| ✅ | Voir points (total + historique basique) |

## 🏠 Dashboard

| Statut | Tâche |
|--------|-------|
| ✅ | Voir matières |
| 🔶 | Voir activités / devoirs |
| 🔶 | Voir examens actifs |
| 🔶 | Voir annonces |
| 🔶 | Voir calendrier / événements |
| ⬜ | Voir progression globale |

## 📖 Lecture

| Statut | Tâche |
|--------|-------|
| 🔶 | Lire texte plein écran |
| ✅ | Retour lecture ↔ exercice |
| ⬜ | Surligner texte |
| ⬜ | Reprendre où on s’est arrêté |

## 📝 Activités interactives

| Statut | Tâche |
|--------|-------|
| 🔶 | Écrire / dessiner (module problèmes) |
| ⬜ | Surligner / effacer complet |
| ⬜ | Changer de page |
| ✅ | Sauvegarde automatique |
| ✅ | Soumettre activité |
| 🔶 | Voir résultat après correction |
| ⬜ | Voir annotations prof |
| ⬜ | Refaire après renvoi prof |

## ❓ Questions

| Statut | Tâche |
|--------|-------|
| 🔶 | Répondre aux questions |
| 🔶 | Passer étapes / sections |
| ⬜ | Audio / vidéo dans questions |

## 📋 Examens

| Statut | Tâche |
|--------|-------|
| ✅ | Commencer examen |
| ✅ | Voir minuterie |
| ⬜ | Auto-soumission |
| ✅ | Soumettre examen |
| 🔶 | Voir résultats + commentaires |

## ⭐ Points

| Statut | Tâche |
|--------|-------|
| ✅ | Voir historique |
| ⬜ | Voir classement |
| ⬜ | Comprendre chaque point (raison détaillée) |

## 📅 Organisation

| Statut | Tâche |
|--------|-------|
| 🔶 | Voir calendrier |
| ⬜ | Voir examens à venir |
| ⬜ | Voir devoirs à venir |

## 📢 Communication

| Statut | Tâche |
|--------|-------|
| ✅ | Lire annonces |
| ⬜ | Voir rappels |

## 🔒 Restrictions

| Statut | Tâche |
|--------|-------|
| ✅ | Impossible accéder admin |
| ✅ | Impossible modifier notes |
| ✅ | Impossible voir réponses autres élèves |

---

# BONUS FUTURS (hors MVP)

- Mode sombre
- Notifications push
- Sons d’interaction
- Barre de progression globale
- Classement hebdomadaire
- Mini effets visuels (confettis points)

---

# Priorités de développement recommandées

1. **Moteur pages interactives** multi-pages + plein écran + autosave
2. **Correction à l’encre** admin + renvoi + visibilité élève
3. **Lecture plein écran** finalisée (zoom, surlignage, position)
4. **Points ClassDojo** (grille + popup)
5. **Auto-submit examen** + une tentative
6. **Refonte UI** tablette premium
7. **15 types de questions** interactifs complets
8. **Bulletins** PDF avec moyennes matières

---

*Document maintenu dans le dépôt : `docs/CAHIER_DES_CHARGES.md`*
