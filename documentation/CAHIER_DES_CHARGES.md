# EDUSPHERE ÔÇö Cahier des charges ultime + TODO LIST

> **Version** : 3.0 ÔÇö document de r├®f├®rence unique  
> **Public** : ├®cole dÔÇÖ├®t├® personnalis├®e, enfants 7ÔÇô10 ans, 1 prof/admin, plusieurs ├®l├¿ves  
> **Philosophie** : **TABLETTE FIRST** (iPad / tablette Android), puis ordinateur  
> **Contenu** : 100 % cr├®├® manuellement par lÔÇÖadmin ÔÇö **aucune IA**

---

## Liens GitLab (important)

| Ressource | URL |
|-----------|-----|
| **D├®p├┤t code** | https://gitlab.com/omrannajdi33-group/edusphere |
| **Pipelines CI/CD** | https://gitlab.com/omrannajdi33-group/edusphere/-/pipelines |
| **Guide d├®ploiement** | [deploy/DEPLOIEMENT.md](../deploy/DEPLOIEMENT.md) |

### Pourquoi GitLab Pages affiche 404

`https://omrannajdi33-group.gitlab.io/edusphere/` ne peut **pas** h├®berger EduSphere tel quel.

GitLab Pages sert du **HTML/CSS/JS statique** uniquement. EduSphere exige :

- PHP (Laravel)
- MySQL (ou SQLite en local)
- Sessions, authentification, uploads, API interne

**Workflow correct :**

```
D├®veloppement local (Laragon / artisan serve)
        Ôåô
Versioning + CI (GitLab)
        Ôåô
D├®ploiement r├®el (VPS via deploy:production, Railway, Render, HostingerÔÇª)
```

Le fichier `.gitlab-ci.yml` sert ├á **tester, builder les assets et d├®ployer sur un vrai serveur**, pas ├á publier Laravel sur Pages.

---

## 1. Vision du projet

EduSphere nÔÇÖest **pas** un clone de Google Classroom avec des ┬½ cours ┬╗ ├á ajouter/supprimer.

CÔÇÖest une **plateforme edtech modulaire** qui fusionne les id├®es de :

- **ClassDojo** (points, grille ├®l├¿ves, feedback visible)
- **Goodnotes / Notability** (pages interactives, dessin, surlignage, correction ├á lÔÇÖencre)
- **Khan Academy / Duolingo** (progression par comp├®tence, interfaces sp├®cialis├®es)

### Principe architectural central

```
Mati├¿re
 ÔööÔöÇÔöÇ Comp├®tence (module sp├®cialis├®)
      ÔööÔöÇÔöÇ Activit├® (exercice OU examen)
           ÔööÔöÇÔöÇ Pages interactives internes + questions adapt├®es
```

Chaque **comp├®tence** poss├¿de :

- sa propre interface (pas un formulaire g├®n├®rique)
- ses propres outils (canvas, audio, carte, checklistÔÇª)
- sa propre logique p├®dagogique
- ses propres types de questions pertinents

---

## 2. Technologies impos├®es

| Couche | Stack |
|--------|--------|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Base de donn├®es | MySQL (production) ┬À SQLite (dev local) |
| Local | Laragon ou `php artisan serve` |
| Versioning / CI | GitLab (`.gitlab-ci.yml`) |
| PWA | `manifest.json`, mode standalone, ic├┤ne, splash |

---

## 3. R├┤les utilisateurs

### Admin / Professeur (r├┤le unique)

Acc├¿s complet : ├®l├¿ves, mati├¿res, comp├®tences, activit├®s, correction, points, bulletins, calendrier, annonces.

### ├ël├¿ve

Acc├¿s limit├® : mati├¿res assign├®es, activit├®s/examens de son niveau, lecture, travail interactif, r├®sultats, points, annonces.

**Restrictions obligatoires :**

- impossible dÔÇÖacc├®der ├á `/admin`
- impossible de modifier ses notes
- impossible de voir le travail des autres ├®l├¿ves

---

## 4. Design global (TABLETTE FIRST)

### Objectif visuel

Design **premium, ultra moderne, minimaliste, tactile**, inspir├® de Apple, Linear, Notion, ClassDojo, Goodnotes.

### Exigences UI

- cartes flottantes, coins tr├¿s arrondis (XL), ombres douces
- glassmorphism l├®ger, animations fluides, transitions premium
- **gros boutons**, navigation simple, lisible pour enfants 7ÔÇô10 ans
- **plein ├®cran** sur activit├®s, lecture et examens (pas de chrome inutile)
- sur iPad : mode standalone PWA, support stylet (Apple Pencil) et doigt
- couleurs par mati├¿re coh├®rentes partout (dashboard, cartes, barres dÔÇÖ├®tat)

### Palette mati├¿res (cible)

| Mati├¿re | Couleur |
|---------|---------|
| Fran├ºais | Bleu |
| Math├®matiques | Violet |
| Sciences | Vert |
| Histoire | Orange |
| G├®ographie | Cyan |
| Islam | Emerald |
| Natation | Aqua |
| ├ëducation physique | Rouge doux |

> **├ëtat actuel** : refonte CSS en cours (`tokens.css`, `edu-base.css`) ÔÇö palette et dashboards ├á finaliser.

---

## 5. Niveaux scolaires & assignation

Chaque ├®l├¿ve poss├¿de :

- **niveau scolaire** (ex. 2e, 3e ann├®e ÔÇö enum `StudentLevel`)
- avatar, points cumul├®s, progression

Lors de la cr├®ation dÔÇÖune activit├®, lÔÇÖadmin choisit :

1. **niveau cible**
2. **├®l├¿ves assign├®s** (relation `activity_student`)
3. **type** : exercice **ou** examen
4. **mode correction** : automatique **ou** manuelle

LÔÇÖ├®l├¿ve ne voit **que** les activit├®s qui lui sont assign├®es et qui correspondent ├á son niveau.

---

## 6. Mati├¿res officielles (programme qu├®b├®cois primaire)

Pas de syst├¿me ┬½ ajouter un cours ┬╗. Les mati├¿res sont **pr├®d├®finies** avec leurs comp├®tences, align├®es sur le programme qu├®b├®cois (fran├ºais langue dÔÇÖenseignement, math├®matique, univers social, science et technologie, ├®ducation ├á la vie affective / valeurs pour lÔÇÖIslam, etc.).

R├®f├®rences utiles :

- [Programme de formation de lÔÇÖ├®cole qu├®b├®coise ÔÇö primaire](https://www.quebec.ca/education/programmes-formation/ecole-quebecoise/primaire)
- Domaines fran├ºais : **lire**, **├®crire**, **communiquer oralement**
- Math├®matique : **r├®soudre une situation-probl├¿me**, **d├®velopper et d├®ployer des outils math├®matiques**, **d├®ployer des raisonnements**
- Science : **Univers vivant**, **Univers mat├®riel**, **Terre et espace**, **Techniques et technologies**
- Univers social : **Temps**, **Espace**, **Soci├®t├®**

### Liste des mati├¿res EduSphere

1. **Fran├ºais**
2. **Math├®matiques**
3. **Sciences**
4. **├ëducation physique**
5. **Natation** *(mati├¿re sp├®ciale ├á part enti├¿re)*
6. **Histoire**
7. **G├®ographie**
8. **Islam**

Chaque mati├¿re a : couleur, ic├┤ne, comp├®tences, modules UI d├®di├®s.

---

## 7. Comp├®tences & modules par mati├¿re

> Chaque comp├®tence avec `module_type` poss├¿de une vue Blade + JS Alpine d├®di├®s dans `resources/views/modules/{type}/` et `resources/js/modules/`.

### ­ƒç½­ƒçÀ Fran├ºais

| Comp├®tence | Module | Interface sp├®cialis├®e |
|------------|--------|------------------------|
| Lecture / compr├®hension | `lecture` | Texte plein ├®cran, surlignage, zoom, retour lecture Ôåö exercice |
| ├ëcriture | `ecriture` | Grande zone type Goodnotes/Docs : gras, italique, surlignage, compteur mots |
| Communication orale | `oral` | Enregistrement audio, minuterie, r├®├®coute, d├®p├┤t |
| Vocabulaire | *(g├®n├®rique)* | ├Ç sp├®cialiser |
| Orthographe | *(g├®n├®rique)* | ├Ç sp├®cialiser |
| Grammaire | *(g├®n├®rique)* | ├Ç sp├®cialiser |

### Ô×ù Math├®matiques

| Comp├®tence | Module | Interface sp├®cialis├®e |
|------------|--------|------------------------|
| Arithm├®tique | `calcul` | Clavier math, r├®ponses ├®tape par ├®tape |
| R├®solution de probl├¿mes | `problemes` | ├ënonc├® + **brouillon plein ├®cran** (dessin, surligneur, r├®ponse en popup) |
| G├®om├®trie | `geometrie` | Tracer lignes, formes, glisser-d├®poser |
| Mesures, Fractions, LogiqueÔÇª | *(g├®n├®rique)* | Modules d├®di├®s ├á cr├®er |

### ­ƒö¼ Sciences

| Comp├®tence | Module | Interface sp├®cialis├®e |
|------------|--------|------------------------|
| Observation | `observation` | Images annotables, l├®gendes, identification |
| Exp├®rimentation | `experience` | ├ëtapes, tableaux dÔÇÖobservations, r├®ponses ouvertes |
| Univers vivant / mat├®riel / Terre | *(g├®n├®rique)* | ├Ç sp├®cialiser |

### ­ƒîì Histoire & G├®ographie

| Comp├®tence | Module | Interface sp├®cialis├®e |
|------------|--------|------------------------|
| Temps historique | `timeline` | Frise, glisser-d├®poser, ordre chronologique |
| Cartes / Territoires | `carte` | Carte interactive, placer/relier ├®l├®ments |

### Ôÿ¬´©Å Islam

| Comp├®tence | Module | Interface sp├®cialis├®e |
|------------|--------|------------------------|
| Lecture | `lecture_islamique` | Texte arabe, audio, r├®p├®tition |
| Histoire islamique | `histoire_islamique` | Histoires, quiz, chronologie |

### ­ƒÅâ ├ëducation physique

| Comp├®tence | Module | Interface sp├®cialis├®e |
|------------|--------|------------------------|
| Coordination / activit├® | `activite` | Checklist, minuterie, validation prof |

### ­ƒÅè Natation

| Comp├®tence | Module | Interface sp├®cialis├®e |
|------------|--------|------------------------|
| Flottaison / techniques | `techniques` | Vid├®os, checklist progression, validation manuelle |
| S├®curit├® aquatique | `securite` | Sc├®narios, quiz s├®curit├®, images interactives |

---

## 8. Lecture interactive (OBLIGATOIRE)

Avant certains exercices/examens, lÔÇÖ├®l├¿ve **doit lire un texte**.

### Contenu du texte (admin)

- saisie manuelle dans lÔÇÖadmin **OU**
- import de contenu riche (texte structur├® ÔÇö **pas de d├®pendance PDF** pour le flux principal)

### Mode lecture plein ├®cran

- le texte **occupe toute la page**
- lisible tablette, zoom, surlignement
- lecture ligne par ligne (optionnel)
- sauvegarde de la position de lecture
- **consultable ├á tout moment** pendant lÔÇÖexercice ou lÔÇÖexamen

### Navigation

- bouton **┬½ Retour exercice ┬╗**
- bouton **┬½ Retour lecture ┬╗**
- transitions fluides, sans perdre le travail en cours

---

## 9. Moteur de pages interactives (C┼ÆUR DU PROJET)

### ÔØî Ce quÔÇÖon ne veut PLUS

- Importer des PDF comme support principal dÔÇÖactivit├®
- Extensions/plugins PDF externes pour lÔÇÖ├®criture

### Ô£à Ce quÔÇÖon veut

**Moteur interne propri├®taire** ÔÇö chaque activit├® = suite de **pages num├®riques** stock├®es en base.

#### Fonctionnalit├®s ├®l├¿ve (plein ├®cran)

- ├®crire (stylet / doigt)
- dessiner (stylo, surligneur, gomme)
- surligner
- effacer (partiel / page enti├¿re)
- **changer de page** (pagination claire)
- **sauvegarde automatique** (autosave)
- reprendre plus tard
- soumettre

#### Fonctionnalit├®s professeur

- voir le travail soumis page par page
- **corriger ├á lÔÇÖencre** (annotations par-dessus le travail ├®l├¿ve)
- commenter, noter
- **recorriger** et **renvoyer** pour que lÔÇÖ├®l├¿ve refasse ses erreurs
- conserver **historique des versions** (soumissions + corrections)

#### Workflow correction

```
1. ├ël├¿ve soumet
2. Prof corrige ├á lÔÇÖencre + note + commentaires
3. Prof renvoie (statut ┬½ ├á refaire ┬╗ ou ├®quivalent)
4. ├ël├¿ve corrige et resoumet
5. Prof recorrige
ÔåÆ Tout reste visible pour lÔÇÖ├®l├¿ve (r├®sultat, annotations, historique)
```

> **├ëtat actuel** : canvas partiel sur module `problemes` et feuilles r├®ponses ; moteur multi-pages unifi├® et correction ├á lÔÇÖencre admin **├á construire**.

---

## 10. Activit├®s : exercice vs examen

├Ç la cr├®ation, lÔÇÖadmin choisit **`purpose`** :

| | Exercice | Examen |
|---|----------|--------|
| Barre visuelle | Bleue ┬½ Exercice ┬╗ | Rouge ┬½ Examen ┬╗ |
| Minuterie | Optionnelle | Obligatoire (`exam_duration_minutes`) |
| Tentatives | Plus souple | **Une seule tentative** (cible) |
| Auto-soumission | Non | **Oui** ├á expiration du timer (cible) |
| Comptabilisation bulletin | Non | Oui (`counts_for_bulletin`) |
| Correction | Auto ou manuelle (`grading_mode`) | Souvent manuelle |
| Grille correction manuelle | Genre, effort, compr├®hension, expression, orthographe, progression | Idem |

---

## 11. Types de questions obligatoires (15)

Le moteur dynamique doit supporter :

1. Choix multiples (QCM)
2. Vrai / Faux
3. R├®ponse courte
4. R├®ponse longue
5. Relier les ├®l├®ments
6. Mettre dans lÔÇÖordre
7. Compl├®ter les trous
8. S├®lection multiple
9. Associer image et r├®ponse
10. Calcul num├®rique
11. Glisser-d├®poser
12. Identifier sur image
13. Question audio
14. Tableau ├á compl├®ter
15. Cases ├á cocher

> **├ëtat actuel** : enum `QuestionType` + rendu partiel dans `_question.blade.php` ÔÇö tous les types interactifs ne sont pas encore impl├®ment├®s.

---

## 12. Syst├¿me de points (ClassDojo)

### Interface admin

- ├®l├¿ves affich├®s en **GRILLE** (cartes), pas en liste
- chaque carte : avatar, nom, total points
- clic sur un ├®l├¿ve ÔåÆ **popup** avec :
  - section **Positif** (participation, effort, respect, excellent travailÔÇª)
  - section **N├®gatif** (distraction, retard, manque dÔÇÖeffortÔÇª)
- chaque action configurable : **nom + valeur points** (`point_behaviors`)

### Interface ├®l├¿ve

- voir total, historique, **pourquoi** les points ont ├®t├® donn├®s/retir├®s
- voir classement (optionnel)
- voir sa progression

> **├ëtat actuel** : CRUD points + behaviors en base ; **grille ClassDojo + popup** ├á refaire.

---

## 13. R├®sultats, bulletins & visibilit├® ├®l├¿ve

LÔÇÖ├®l├¿ve **doit** pouvoir voir :

- ses notes et r├®sultats dÔÇÖexamens
- les corrections et annotations du prof
- les commentaires
- sa progression par mati├¿re

Le syst├¿me admin doit g├®n├®rer :

- moyenne g├®n├®rale
- moyenne par mati├¿re
- statistiques, progression, bulletins PDF/HTML (cible)

> **├ëtat actuel** : pages r├®sultat activit├® + bulletins admin basiques ; affichage annotations et PDF bulletins **incomplet**.

---

## 14. Organisation scolaire

- horaire hebdomadaire
- calendrier (├®v├®nements, examens, devoirs)
- annonces (publi├®es, modifiables, suppressibles)
- rappels importants sur dashboard ├®l├¿ve

> **├ëtat actuel** : mod├¿les `CalendarEvent`, `Announcement` + affichage partiel dashboard.

---

## 15. PWA / Application tablette

| Exigence | D├®tail |
|----------|--------|
| manifest.json | Nom, ic├┤nes, `display: standalone` |
| Splash screen | Au lancement depuis ├®cran dÔÇÖaccueil |
| Plein ├®cran | Masquer UI navigateur |
| Tactile | Zones touch ÔëÑ 44px, gestes simples |
| Offline partiel | Cache assets statiques (cible) |
| iPad | `viewport-fit=cover`, safe areas |

Installation : Chrome ÔåÆ **Ajouter ├á lÔÇÖ├®cran dÔÇÖaccueil**.

---

## 16. S├®curit├®

- routes admin prot├®g├®es (`middleware admin`)
- routes ├®l├¿ve prot├®g├®es (`middleware student`)
- mots de passe hash├®s (bcrypt)
- validation Laravel c├┤t├® serveur
- CSRF sur tous les formulaires
- examens : verrouillage apr├¿s soumission / expiration
- sauvegarde automatique du travail ├®l├¿ve (anti-perte)

---

## 17. ├ëtat dÔÇÖavancement du code (r├®sum├®)

| Domaine | Statut |
|---------|--------|
| Auth login/logout | Ô£à Fait |
| CRUD ├®l├¿ves admin | Ô£à Fait |
| 8 mati├¿res + comp├®tences seed├®es | Ô£à Fait |
| 15 modules comp├®tence (vues de base) | Ô£à Partiel |
| Activit├® exercice/examen + timer | Ô£à Partiel (auto-submit timer ├á finir) |
| Correction auto/manuelle + grille 6 crit├¿res | Ô£à Partiel |
| Texte lecture + lien activit├® | Ô£à Partiel (plein ├®cran ├á peaufiner) |
| Canvas brouillon (probl├¿mes) | Ô£à Partiel |
| Moteur pages multi-pages interne | ÔØî ├Ç faire |
| Correction ├á lÔÇÖencre + renvoi + historique | ÔØî ├Ç faire |
| 15 types de questions interactifs | ÔØî Partiel |
| Points grille ClassDojo | ÔØî ├Ç faire |
| Bulletins complets | ÔØî Partiel |
| Design ultra moderne tablette | ÔØî En cours |
| PWA compl├¿te | ÔØî Partiel (`manifest.json` existe) |
| CI/CD GitLab test + build + deploy SSH | Ô£à Fait |
| GitLab Pages | ÔØî Non applicable (Laravel) |

---

# TODO LIST ÔÇö ADMIN / PROFESSEUR

> L├®gende : Ô£à fait ┬À ­ƒöÂ partiel ┬À Ô¼£ ├á faire

## ­ƒæñ Comptes

| Statut | T├óche |
|--------|-------|
| Ô£à | Se connecter |
| Ô£à | Se d├®connecter |
| Ô¼£ | Modifier profil admin |
| Ô¼£ | Changer mot de passe |

## ­ƒæ¿ÔÇì­ƒÄô ├ël├¿ves

| Statut | T├óche |
|--------|-------|
| Ô£à | Ajouter ├®l├¿ve |
| Ô£à | Modifier ├®l├¿ve |
| Ô£à | Supprimer ├®l├¿ve |
| Ô£à | R├®initialiser mot de passe |
| ­ƒöÂ | Ajouter avatar (champ existe, UI ├á enrichir) |
| ­ƒöÂ | D├®finir niveau scolaire par ├®l├¿ve |
| Ô¼£ | Voir profil ├®l├¿ve d├®taill├® |
| Ô¼£ | Voir progression / statistiques ├®l├¿ve |

## ­ƒôÜ Mati├¿res & comp├®tences

| Statut | T├óche |
|--------|-------|
| Ô£à | Mati├¿res pr├®d├®finies (8 mati├¿res) |
| Ô£à | Comp├®tences par mati├¿re (seed Qu├®bec) |
| Ô£à | Couleurs et ic├┤nes mati├¿res |
| Ô¼£ | Admin : ├®diter comp├®tences sans code |
| Ô¼£ | Modules UI pour comp├®tences sans `module_type` |

## ­ƒôû Lecture interactive

| Statut | T├óche |
|--------|-------|
| Ô£à | Saisir texte lecture dans activit├® |
| ­ƒöÂ | Import contenu riche (non-PDF) |
| ­ƒöÂ | Mode lecture plein ├®cran |
| Ô£à | Relier texte Ôåö exercice/examen |
| Ô¼£ | Zoom + surlignement lecture |
| Ô¼£ | Lecture ligne par ligne |
| Ô¼£ | Sauvegarde position lecture |
| Ô£à | Boutons retour lecture / retour exercice |

## ­ƒôØ Moteur pages interactives

| Statut | T├óche |
|--------|-------|
| Ô¼£ | Cr├®er activit├® multi-pages interne |
| Ô¼£ | Navigation pages (suivante / pr├®c├®dente) |
| Ô¼£ | ├ëcrire + dessiner + surligner + effacer |
| Ô¼£ | Sauvegarde automatique par page |
| Ô¼£ | Plein ├®cran obligatoire |
| Ô¼£ | Soumettre activit├® compl├¿te |
| Ô¼£ | Voir soumission page par page (admin) |
| Ô¼£ | Corriger ├á lÔÇÖencre |
| Ô¼£ | Recorriger + renvoyer ├á lÔÇÖ├®l├¿ve |
| Ô¼£ | Historique versions |

## ÔØô Questions dynamiques

| Statut | T├óche |
|--------|-------|
| Ô£à | Cr├®er activit├® avec sections/questions |
| ­ƒöÂ | 15 types de questions (rendu partiel) |
| Ô¼£ | Images dans questions |
| Ô¼£ | Audio dans questions |
| Ô¼£ | Pages / ├®tapes / sections avanc├®es |
| Ô£à | Sauvegarde automatique r├®ponses (progression) |
| Ô£à | Correction automatique (QCM etc.) |
| Ô£à | Correction manuelle + grille 6 crit├¿res |

## ­ƒôï Examens

| Statut | T├óche |
|--------|-------|
| Ô£à | Choisir type examen vs exercice |
| Ô£à | Minuterie examen (affichage) |
| Ô¼£ | Auto-soumission fin du temps |
| Ô¼£ | Une seule tentative stricte |
| Ô¼£ | Date/heure ouverture / fermeture |
| Ô¼£ | Lancer / fermer examen manuellement |
| Ô£à | Voir examens soumis (corrections) |
| ­ƒöÂ | Publier r├®sultats visibles ├®l├¿ve |

## Ô¡É Points (ClassDojo)

| Statut | T├óche |
|--------|-------|
| Ô£à | Ajouter / retirer points |
| Ô£à | Actions positives/n├®gatives configurables |
| Ô¼£ | Grille ├®l├¿ves (cartes) |
| Ô¼£ | Popup points au clic |
| Ô¼£ | Classement ├®l├¿ves |

## ­ƒôè Bulletins & r├®sultats

| Statut | T├óche |
|--------|-------|
| ­ƒöÂ | Page bulletins admin |
| Ô¼£ | Moyenne g├®n├®rale |
| Ô¼£ | Moyenne par mati├¿re |
| Ô¼£ | Export bulletin PDF |
| Ô¼£ | Progression d├®taill├®e |

## ­ƒôà Organisation

| Statut | T├óche |
|--------|-------|
| ­ƒöÂ | Calendrier / ├®v├®nements (mod├¿le + affichage) |
| Ô¼£ | Horaire hebdomadaire ├®ditable |
| Ô¼£ | Devoirs sur calendrier |
| Ô¼£ | Examens sur calendrier |

## ­ƒôó Communication

| Statut | T├óche |
|--------|-------|
| Ô£à | Publier annonces |
| Ô£à | Modifier / supprimer annonces |
| Ô¼£ | Rappels importants dashboard |

## ­ƒô▒ PWA & tablette

| Statut | T├óche |
|--------|-------|
| ­ƒöÂ | manifest.json |
| Ô¼£ | Splash screen complet |
| Ô¼£ | Ic├┤nes toutes tailles |
| Ô¼£ | Mode standalone test├® iPad |
| Ô¼£ | Support Apple Pencil optimis├® |

## ­ƒÄ¿ UI / UX

| Statut | T├óche |
|--------|-------|
| ­ƒöÂ | Dashboard admin moderne |
| ­ƒöÂ | Dashboard ├®l├¿ve moderne |
| Ô¼£ | Refonte design ultra premium |
| Ô¼£ | Animations fluides globales |
| Ô¼£ | Palette mati├¿res finalis├®e |

---

# TODO LIST ÔÇö ├ëL├êVE

## ­ƒöÉ Compte

| Statut | T├óche |
|--------|-------|
| Ô£à | Se connecter |
| Ô£à | Se d├®connecter |
| ­ƒöÂ | Voir profil / avatar |
| Ô£à | Voir points (total + historique basique) |

## ­ƒÅá Dashboard

| Statut | T├óche |
|--------|-------|
| Ô£à | Voir mati├¿res |
| ­ƒöÂ | Voir activit├®s / devoirs |
| ­ƒöÂ | Voir examens actifs |
| ­ƒöÂ | Voir annonces |
| ­ƒöÂ | Voir calendrier / ├®v├®nements |
| Ô¼£ | Voir progression globale |

## ­ƒôû Lecture

| Statut | T├óche |
|--------|-------|
| ­ƒöÂ | Lire texte plein ├®cran |
| Ô£à | Retour lecture Ôåö exercice |
| Ô¼£ | Surligner texte |
| Ô¼£ | Reprendre o├╣ on sÔÇÖest arr├¬t├® |

## ­ƒôØ Activit├®s interactives

| Statut | T├óche |
|--------|-------|
| ­ƒöÂ | ├ëcrire / dessiner (module probl├¿mes) |
| Ô¼£ | Surligner / effacer complet |
| Ô¼£ | Changer de page |
| Ô£à | Sauvegarde automatique |
| Ô£à | Soumettre activit├® |
| ­ƒöÂ | Voir r├®sultat apr├¿s correction |
| Ô¼£ | Voir annotations prof |
| Ô¼£ | Refaire apr├¿s renvoi prof |

## ÔØô Questions

| Statut | T├óche |
|--------|-------|
| ­ƒöÂ | R├®pondre aux questions |
| ­ƒöÂ | Passer ├®tapes / sections |
| Ô¼£ | Audio / vid├®o dans questions |

## ­ƒôï Examens

| Statut | T├óche |
|--------|-------|
| Ô£à | Commencer examen |
| Ô£à | Voir minuterie |
| Ô¼£ | Auto-soumission |
| Ô£à | Soumettre examen |
| ­ƒöÂ | Voir r├®sultats + commentaires |

## Ô¡É Points

| Statut | T├óche |
|--------|-------|
| Ô£à | Voir historique |
| Ô¼£ | Voir classement |
| Ô¼£ | Comprendre chaque point (raison d├®taill├®e) |

## ­ƒôà Organisation

| Statut | T├óche |
|--------|-------|
| ­ƒöÂ | Voir calendrier |
| Ô¼£ | Voir examens ├á venir |
| Ô¼£ | Voir devoirs ├á venir |

## ­ƒôó Communication

| Statut | T├óche |
|--------|-------|
| Ô£à | Lire annonces |
| Ô¼£ | Voir rappels |

## ­ƒöÆ Restrictions

| Statut | T├óche |
|--------|-------|
| Ô£à | Impossible acc├®der admin |
| Ô£à | Impossible modifier notes |
| Ô£à | Impossible voir r├®ponses autres ├®l├¿ves |

---

# BONUS FUTURS (hors MVP)

- Mode sombre
- Notifications push
- Sons dÔÇÖinteraction
- Barre de progression globale
- Classement hebdomadaire
- Mini effets visuels (confettis points)

---

# Priorit├®s de d├®veloppement recommand├®es

1. **Moteur pages interactives** multi-pages + plein ├®cran + autosave
2. **Correction ├á lÔÇÖencre** admin + renvoi + visibilit├® ├®l├¿ve
3. **Lecture plein ├®cran** finalis├®e (zoom, surlignage, position)
4. **Points ClassDojo** (grille + popup)
5. **Auto-submit examen** + une tentative
6. **Refonte UI** tablette premium
7. **15 types de questions** interactifs complets
8. **Bulletins** PDF avec moyennes mati├¿res

---

*Document maintenu dans le d├®p├┤t : `docs/CAHIER_DES_CHARGES.md`*
