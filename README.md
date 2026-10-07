# Gestion des convocations aux épreuves E5/E6 — BTS SIO2

Application de gestion des convocations aux épreuves informatiques évaluées
en CCF (E5 et E6), développée en Laravel dans le cadre de l'atelier
professionnel de BTS SIO2 (Lycée Saint Joseph).

## Rôles gérés

Les 4 rôles sont fonctionnels :

- **Administrateur** : gestion des utilisateurs (création, modification du
  rôle/statut, blocage de compte) et paramétrage des tables de référence
  (rôles, statuts, genres)
- **Gestionnaire des épreuves** : création d'une épreuve (avec ajout de lieu
  à la volée), affectation des jurys (détection des chevauchements
  horaires et des conflits d'intérêt possibles), consultation des
  convocations d'une épreuve
- **Candidat** : consultation de ses convocations (une par épreuve passée)
- **Membre du jury** : consultation de son planning et des candidats à
  évaluer (gère le cas d'un jury membre de plusieurs panels)

## Prérequis

- PHP 8.2+ (mode FPM recommandé)
- Composer
- Git
- MySQL / MariaDB
- Un client FTP (ex. FileZilla), uniquement pour un déploiement sur le
  serveur distant de l'établissement — voir la documentation d'installation

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
4. **Ne pas exécuter `php artisan migrate`** : la structure de la base est
   fournie par des scripts SQL dédiés, à exécuter dans cet ordre (via
   phpMyAdmin, un fichier à la fois) :
   1. Script de création des tables (fourni par l'enseignant référent)
   2. `init.sql` — données de référence (rôles, statuts, genres, compte admin)
   3. `test.sql` — jeu de données réaliste pour le développement
5. Lancer le serveur de développement :
   ```bash
   php artisan serve
   ```
   L'application est alors accessible sur `http://127.0.0.1:8000` (la page
   d'accueil redirige automatiquement vers `/login`).

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
  changements dans sa propre branche avec `git fetch origin` puis
  `git merge origin/main`

## Comptes de test

Compte administrateur par défaut (créé par `init.sql`) :
`admin@saintjo.local` / `password`.

Voir la documentation d'installation (Annexe « Environnement de test »)
pour la liste complète des comptes de test et leurs mots de passe
(générés par `test.sql`, chacun différent, documenté en clair dans le
champ `commentaire` de `mcd_utilisateurs`).

## Documentation

Fournie séparément (documents Word, avec page de garde et sommaire) :

- **Analyse fonctionnelle** — rôles, actions, données (analyse préalable)
- **Documentation de développement** — mise en place du projet Laravel,
  architecture MVC, structure du code, Git/GitHub
- **Documentation d'installation** — installation locale et déploiement
  sur le serveur distant de l'établissement
- **Documentation utilisateur** — guide d'utilisation par rôle

## Structure technique

- Authentification Laravel « faite main » (pas de scaffolding
  Breeze/Jetstream), car les comptes sont créés par les gestionnaires et
  non par auto-inscription
- Modèles Eloquent alignés sur le MCD fourni : `User` (mcd_users),
  `Utilisateur` (mcd_utilisateurs), `Role`, `Statut`, `Genre`, `Epreuve`,
  `Lieu`, `Jury`, `Passer` — `mcd_former` et `mcd_affecter` sont des tables
  pivots pures, gérées via `belongsToMany()` sans modèle dédié
- **db2laravel** : outil interne fourni par l'enseignant référent
  (`php artisan db2laravel --models`), qui génère des classes de base
  `app/Models/Base/*Base.php` à partir de la structure réelle de la base
  (lecture seule, aucune écriture en base). Ces classes ne sont
  actuellement **pas** utilisées par nos modèles (qui restent nos classes
  écrites à la main) — un bug connu dans le générateur (imports
  `BelongsTo`/`HasMany` manquants) a été signalé à l'enseignant référent
- Menu affiché dynamiquement selon le rôle de l'utilisateur connecté
- Middleware `auth` sur toutes les pages nécessitant une connexion ;
  vérification du rôle (`hasRole()`) et du statut du compte au login

## Limites connues

- Un compte banni pendant qu'il est déjà connecté reste actif jusqu'à sa
  déconnexion (la vérification du statut ne se fait qu'au moment du login)
- Les messages de validation des formulaires sont en anglais par défaut
  (traduction française non encore mise en place)
- Aucun suivi d'état « envoyé » pour les convocations (colonne absente du
  MCD fourni ; page « Voir les convocations » en lecture seule)

## Auteurs

Combari Samuel & Evan — BTS SIO2 — Lycée Saint Joseph