# Gestion des convocations aux épreuves E5/E6 — BTS SIO2

Application de gestion des convocations aux épreuves informatiques évaluées
en CCF (E5 et E6), développée en Laravel dans le cadre de l'atelier
professionnel de BTS SIO2 (Lycée Saint Joseph).

Technologies : **Laravel 12**, PHP 8.3 minimum, MySQL / MariaDB,
**Laravel Fortify** (authentification) et **Livewire** (page de gestion
des comptes).

## Rôles gérés

L'application gère 5 rôles (table `mcd_roles`) :

| Code       | Rôle                      | Fonctionnalités |
|------------|---------------------------|-----------------|
| `ADMIN`    | Administrateur            | Gestion des utilisateurs (création, recherche, activation des comptes inscrits, modification du rôle/statut, blocage de compte) et paramétrage des tables de référence (rôles, statuts, genres) |
| `GEST`     | Gestionnaire des épreuves | Création d'une épreuve (avec ajout de lieu à la volée), affectation des jurys (détection des chevauchements horaires et des conflits d'intérêt possibles), consultation des convocations d'une épreuve |
| `CAND`     | Candidat                  | Consultation de ses convocations (une par épreuve passée) |
| `JURY_ENS` | Jury enseignant           | Consultation de son planning et des candidats à évaluer (gère le cas d'un jury membre de plusieurs panels) |
| `JURY_PRO` | Jury professionnel        | Identique à `JURY_ENS` |

L'ancien rôle unique `JURY` a été scindé en `JURY_ENS` et `JURY_PRO` (décision
du professeur). Les deux rôles jury ont les mêmes pages et le même menu : le
test est centralisé dans la méthode `User::isJury()`, qui renvoie
`hasRole('JURY_ENS') || hasRole('JURY_PRO')`.

**Rôle et statut sont indépendants :**

- le **rôle** (`mcd_roles`) détermine ce que l'utilisateur peut faire ;
- le **statut** (`mcd_statuts`) détermine si le compte est utilisable :
  `A` (Actif), `I` (Inactif), `B` (Banni). Seul le statut `A` permet de se
  connecter.

## Authentification et pages publiques

L'authentification est gérée par **Laravel Fortify** (et non plus par un
`LoginController` écrit à la main). Fortify fournit les routes et les
contrôleurs ; le projet fournit les vues Blade et ses règles, dans
`app/Providers/FortifyServiceProvider.php` et `app/Actions/Fortify/`.

| Page | Rôle |
|------|------|
| `/` | Page d'accueil publique (boutons « Se connecter » et « S'inscrire ») ; redirige vers `/menu` si l'utilisateur est déjà connecté |
| `/login` | Connexion, puis redirection vers `/menu` |
| `/register` | Inscription publique |
| `/forgot-password` | Demande d'un lien de réinitialisation du mot de passe (lien valable 60 minutes, utilisable une seule fois) |

**Connexion** : seul un compte au statut `A` peut se connecter. Messages
affichés :

- « Identifiants incorrects. » (e-mail inconnu ou mauvais mot de passe) ;
- « Ce compte est désactivé. » (statut `I`) ;
- « Ce compte est bloqué. » (statut `B` ou autre).

Après 5 échecs en une minute pour un même e-mail et une même adresse IP,
les tentatives sont bloquées temporairement. La déconnexion ramène sur la
page d'accueil.

**Inscription** : un compte créé depuis `/register` est **toujours** un
Candidat (`CAND`) au statut Inactif (`I`) ; le rôle et le statut ne sont
jamais choisis par l'utilisateur. Un administrateur doit l'activer depuis
« Gestion des utilisateurs » avant qu'il puisse se connecter. Les comptes
des autres rôles (gestionnaires, jurys, administrateurs) sont créés par
l'administrateur.

**Mot de passe oublié** : la réinitialisation ne change que le mot de
passe ; un compte Inactif ou Banni reste refusé à la connexion.

Les messages (validation, connexion, e-mail de réinitialisation) sont en
français : fichiers `lang/fr/` et `lang/fr.json`.

## Prérequis

- PHP 8.3 minimum (mode FPM recommandé)
- Composer
- Git
- MySQL / MariaDB

## Installation (développement local)

1. Cloner le dépôt, puis installer les dépendances :
   ```bash
   composer install
   ```
2. Copier le fichier d'environnement et générer la clé applicative :
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Renseigner la connexion à la base dans `.env` (`DB_HOST`, `DB_DATABASE`,
   `DB_USERNAME`, `DB_PASSWORD`).
4. **Ne pas exécuter `php artisan migrate`** : la structure de la base vient
   de scripts SQL, à importer dans cet ordre, un fichier à la fois, avec
   le client MySQL/MariaDB de votre choix :
   1. `create_v0.sql.txt` — création des tables (fourni par l'enseignant
      référent)
   2. `init.sql` — données de référence (5 rôles, statuts, genres, compte
      admin)
   3. `test.sql` — jeu de données réaliste pour le développement
   4. `ajout_password_reset.sql` — table technique `password_reset_tokens`,
      nécessaire à la fonction « Mot de passe oublié »

   Ces scripts sont fournis séparément : ils ne sont pas dans le dépôt.
5. Lancer le serveur de développement :
   ```bash
   php artisan serve
   ```
   L'application est alors accessible sur `http://127.0.0.1:8000`.
6. E-mails en développement : avec `MAIL_MAILER=log` dans `.env`, aucun
   e-mail n'est réellement envoyé ; il est écrit dans
   `storage/logs/laravel.log`. Pour retrouver le dernier lien de
   réinitialisation du mot de passe :
   ```bash
   grep "reset-password/" storage/logs/laravel.log | tail -1
   ```

**Après chaque `git pull` ou `git merge`** (de nouvelles dépendances ont
pu être ajoutées) :

```bash
composer install
php artisan optimize:clear
```

## Mise à jour d'une base déjà remplie

Si la base contient encore l'ancien rôle `JURY`, exécuter **une seule fois**
le script `migration_roles_jury.sql` (fourni séparément). Il :

1. crée les rôles `JURY_ENS` et `JURY_PRO` ;
2. réaffecte les comptes jury de test vers le bon rôle ;
3. supprime l'ancien rôle `JURY`.

Ne jamais l'exécuter sur une base neuve : `init.sql` contient déjà les 5
rôles.

## Organisation Git (projet réalisé à deux)

Ce projet est développé par Combari Samuel et Evan. Règle à respecter :
**jamais de commit direct sur `main`**.

- Chacun travaille sur sa propre branche (`samuel`, `evan`)
- Avant de commencer une session de travail : `git checkout <sa-branche>`
  puis `git pull`
- Une fois un point fonctionnel et testé : `git add`, `git commit`,
  `git push` sur sa branche
- L'intégration dans `main` se fait via une Pull Request sur GitHub
  (bouton « Compare & pull request », puis « Merge pull request »)
- Après qu'une Pull Request a été fusionnée, l'autre personne récupère ces
  changements dans sa propre branche :
  ```bash
  git fetch origin
  git merge origin/main
  ```

## Comptes de test

Compte administrateur de référence (créé par `init.sql`) :
`admin@saintjo.local` / `password`.

Voir la documentation d'installation (Annexe « Environnement de test »)
pour la liste complète des comptes de test des 5 rôles et leurs mots de
passe (générés par `test.sql`, chacun différent, documenté en clair dans le
champ `commentaire` de `mcd_utilisateurs`).

## Documentation

Fournie séparément (documents Word, avec page de garde et sommaire) :

- **Analyse fonctionnelle des rôles** — rôles, actions, données (analyse
  préalable)
- **Documentation de développement** — mise en place du projet Laravel,
  architecture MVC, structure du code, Git/GitHub
- **Documentation d'installation** — installation locale
- **Documentation utilisateur** — guide d'utilisation par rôle

## Structure technique

- Authentification par **Laravel Fortify**, installé dans le projet
  existant (pas de kit de démarrage, pas de `fortify:install`) ; seules
  l'inscription et la réinitialisation du mot de passe sont activées
  (`config/fortify.php`). Le contrôle du statut est fait dans
  `Fortify::authenticateUsing()`, l'inscription dans
  `app/Actions/Fortify/CreateNewUser.php`, et
  `app/Http/Responses/RegisterResponse.php` déconnecte le nouveau compte
  juste après l'inscription
- Modèles Eloquent alignés sur le MCD fourni : `User` (mcd_users),
  `Utilisateur` (mcd_utilisateurs), `Role`, `Statut`, `Genre`, `Epreuve`,
  `Lieu`, `Jury`, `Passer` — `mcd_former` et `mcd_affecter` sont des tables
  pivots pures, gérées via `belongsToMany()` sans modèle dédié
- Méthodes de contrôle d'accès dans `User` : `hasRole($code)` et
  `isJury()` (vrai pour `JURY_ENS` et `JURY_PRO`)
- Menu affiché dynamiquement selon le rôle de l'utilisateur connecté :
  - `ADMIN` : Gestion des utilisateurs, Paramétrage
  - `GEST` : Créer une session d'épreuves, Affecter les jurys, Envoyer les
    convocations
  - `CAND` : Ma convocation
  - `JURY_ENS` / `JURY_PRO` : Mon planning de jury, Mes candidats à évaluer
- Middleware `auth` sur toutes les pages nécessitant une connexion ;
  vérification du rôle dans chaque contrôleur (erreur 403 sinon) et du
  statut du compte au login
- **Gestion des comptes (ADMIN)** : composant **Livewire**
  `app/Livewire/GestionComptes.php`, vue
  `resources/views/livewire/gestion-comptes.blade.php`. Il permet la
  recherche (nom, prénom, e-mail), le filtre « en attente » (comptes
  Inactifs), les boutons Activer / Désactiver / Bannir et le changement de
  rôle, sans rechargement de la page. L'administrateur connecté ne peut ni
  modifier son propre statut ni retirer son propre rôle ADMIN (même règle
  dans le formulaire « Modifier »). Le rôle ADMIN est revérifié à chaque
  action (erreur 403 sinon)
- **db2laravel** : outil interne fourni par l'enseignant référent
  (`php artisan db2laravel --models`), qui génère des classes de base
  `app/Models/Base/*Base.php` à partir de la structure réelle de la base
  (lecture seule, aucune écriture en base). Ces classes ne sont
  actuellement **pas** utilisées par nos modèles (qui restent nos classes
  écrites à la main) — un bug connu dans le générateur (imports
  `BelongsTo`/`HasMany` manquants) a été signalé à l'enseignant référent

## Limites connues

- Un compte banni pendant qu'il est déjà connecté reste actif jusqu'à sa
  déconnexion (la vérification du statut ne se fait qu'au moment du login)
- Pas d'envoi réel d'e-mails : un serveur SMTP doit être configuré
  (variables `MAIL_*` du `.env`) ; en développement, les e-mails sont
  écrits dans `storage/logs/laravel.log`
- La page « Mot de passe oublié » indique si une adresse e-mail
  correspond à un compte, ce qui permet de savoir si un compte existe
- L'inscription est ouverte à tous : de nombreux comptes inactifs peuvent
  être créés et doivent être validés par l'administrateur (pas de captcha
  ni de vérification de l'adresse e-mail)
- Aucun suivi d'état « envoyé » pour les convocations (colonne absente du
  MCD fourni ; page « Voir les convocations » en lecture seule)

## Auteurs

Combari Samuel & Evan — BTS SIO2 — Lycée Saint Joseph
