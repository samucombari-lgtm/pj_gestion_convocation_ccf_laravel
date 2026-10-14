<?php

use App\Http\Controllers\AffectationController;
use App\Http\Controllers\ConvocationController;
use App\Http\Controllers\EpreuveController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JuryController;
use App\Http\Controllers\LieuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ParametrageController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route publique
|--------------------------------------------------------------------------
*/

// GET / : page d'accueil publique (présentation + Se connecter / S'inscrire).
// HomeController redirige vers /menu si l'utilisateur est déjà connecté.
Route::get('/', [HomeController::class, 'index'])->name('accueil');

/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

// Les routes de connexion, déconnexion et inscription sont fournies par
// Laravel Fortify (configuré dans app/Providers/FortifyServiceProvider.php
// et config/fortify.php), on ne les déclare donc plus ici :
//   GET  /login     (nom "login")    : formulaire de connexion (auth.login)
//   POST /login                      : vérifie email + mot de passe + statut
//   POST /logout    (nom "logout")   : déconnexion, puis retour sur "/"
//   GET  /register  (nom "register") : formulaire d'inscription (auth.register)
//   POST /register                   : crée le compte (CAND, statut 'I')
//   PUT  /user/password (nom "user-password.update") : changement du mot
//                     de passe depuis la page Profil (utilisateur connecté)
// "php artisan route:list" affiche toutes ces routes.

/*
|--------------------------------------------------------------------------
| Pages protégées (accessibles uniquement si connecté)
|--------------------------------------------------------------------------
|
| Deux contrôles, dans CET ordre :
|  1. middleware('auth') sur tout le groupe : un visiteur non connecté est
|     redirigé vers /login ;
|  2. middleware('can:ressource.action') sur chaque route : l'utilisateur
|     connecté doit avoir cette action dans la table mcd_autoriser (via
|     son rôle), sinon erreur 403. Les Gates correspondantes sont créées
|     dans app/Providers/AppServiceProvider.php à partir de mcd_actions.
| Le middleware du groupe passe toujours AVANT celui de la route : un
| visiteur est donc bien redirigé vers /login, et non bloqué en 403.
| /menu et /profil n'ont que 'auth' : ouverts à tout utilisateur connecté.
*/

Route::middleware('auth')->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    // Page Profil, pour tous les rôles : informations du compte (lecture
    // seule) et formulaire de changement du mot de passe (envoyé à la
    // route Fortify PUT /user/password).
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil');

    // Page CAND : liste des convocations du candidat connecté
    Route::get('/ma-convocation', [ConvocationController::class, 'index'])->name('convocation')
        ->middleware('can:convocations.view');

    // Pages JURY : planning des épreuves couvertes, et candidats à évaluer
    Route::get('/jury/planning', [JuryController::class, 'planning'])->name('jury.planning')
        ->middleware('can:planning.viewAny');
    Route::get('/jury/candidats', [JuryController::class, 'candidats'])->name('jury.candidats')
        ->middleware('can:evaluations.viewAny');

    // Pages GEST : gestion des épreuves (session 1/3 : "Créer une session d'épreuves")
    Route::get('/gestion/epreuves', [EpreuveController::class, 'index'])->name('epreuves.index')
        ->middleware('can:epreuves.viewAny');
    Route::get('/gestion/epreuves/creer', [EpreuveController::class, 'create'])->name('epreuves.create')
        ->middleware('can:epreuves.create');
    Route::post('/gestion/epreuves', [EpreuveController::class, 'store'])->name('epreuves.store')
        ->middleware('can:epreuves.create');

    // Création d'un lieu à la volée depuis le formulaire de création d'épreuve
    Route::post('/gestion/lieux', [LieuController::class, 'store'])->name('lieux.store')
        ->middleware('can:lieux.create');

    // Pages GEST : gestion des épreuves (session 2/3 : "Affecter les jurys")
    // {epreuve} dans l'URL est résolu automatiquement en modèle Epreuve
    // par Laravel (route model binding), à partir de son id.
    Route::get('/gestion/epreuves/{epreuve}/affecter', [AffectationController::class, 'create'])->name('epreuves.affecter')
        ->middleware('can:epreuves.update');
    Route::post('/gestion/epreuves/{epreuve}/affecter', [AffectationController::class, 'store'])->name('epreuves.affecter.store')
        ->middleware('can:epreuves.update');

    // Pages GEST : gestion des épreuves (session 3/3 : "Envoyer les convocations")
    // Nom de route au PLURIEL ('epreuves.convocations') pour bien le
    // distinguer de la route candidat 'convocation' (singulier) ci-dessus :
    // ce sont deux pages très différentes malgré le nom proche.
    Route::get('/gestion/epreuves/{epreuve}/convocations', [EpreuveController::class, 'convocations'])->name('epreuves.convocations')
        ->middleware('can:convocations.viewAny');

    // Pages ADMIN : gestion des utilisateurs (étape 1/2)
    // On utilise POST partout (même pour la modification), comme pour le
    // reste de cette appli : pas de @method('PUT') à expliquer en plus,
    // ça reste plus simple pour un formulaire HTML classique.
    Route::get('/admin/utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index')
        ->middleware('can:utilisateurs.viewAny');
    Route::get('/admin/utilisateurs/creer', [UtilisateurController::class, 'create'])->name('utilisateurs.create')
        ->middleware('can:utilisateurs.create');
    Route::post('/admin/utilisateurs', [UtilisateurController::class, 'store'])->name('utilisateurs.store')
        ->middleware('can:utilisateurs.create');
    Route::get('/admin/utilisateurs/{utilisateur}/modifier', [UtilisateurController::class, 'edit'])->name('utilisateurs.edit')
        ->middleware('can:utilisateurs.update');
    Route::post('/admin/utilisateurs/{utilisateur}/modifier', [UtilisateurController::class, 'update'])->name('utilisateurs.update')
        ->middleware('can:utilisateurs.update');

    // Pages ADMIN : paramétrage des tables de référence (étape 2/2)
    // Une page qui liste tout, et un POST d'ajout par table (voir les
    // explications en haut de ParametrageController). Pas de route de
    // suppression : ces valeurs sont utilisées par des clés étrangères.
    Route::get('/admin/parametrage', [ParametrageController::class, 'index'])->name('parametrage.index')
        ->middleware('can:parametrage.viewAny');
    Route::post('/admin/parametrage/roles', [ParametrageController::class, 'storeRole'])->name('parametrage.roles.store')
        ->middleware('can:parametrage.create');
    Route::post('/admin/parametrage/statuts', [ParametrageController::class, 'storeStatut'])->name('parametrage.statuts.store')
        ->middleware('can:parametrage.create');
    Route::post('/admin/parametrage/genres', [ParametrageController::class, 'storeGenre'])->name('parametrage.genres.store')
        ->middleware('can:parametrage.create');
});