<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AffectationController;
use App\Http\Controllers\ConvocationController;
use App\Http\Controllers\EpreuveController;
use App\Http\Controllers\JuryController;
use App\Http\Controllers\LieuController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route publique
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

// GET /login : affiche le formulaire de connexion
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// POST /login : traite l'envoi du formulaire (email + mot de passe)
Route::post('/login', [LoginController::class, 'login']);

// POST /logout : déconnexion (jamais en GET, pour des raisons de sécurité)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Pages protégées (accessibles uniquement si connecté)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    // Page CAND : liste des convocations du candidat connecté
    Route::get('/ma-convocation', [ConvocationController::class, 'index'])->name('convocation');

    // Pages JURY : planning des épreuves couvertes, et candidats à évaluer
    Route::get('/jury/planning', [JuryController::class, 'planning'])->name('jury.planning');
    Route::get('/jury/candidats', [JuryController::class, 'candidats'])->name('jury.candidats');

    // Pages GEST : gestion des épreuves (session 1/3 : "Créer une session d'épreuves")
    Route::get('/gestion/epreuves', [EpreuveController::class, 'index'])->name('epreuves.index');
    Route::get('/gestion/epreuves/creer', [EpreuveController::class, 'create'])->name('epreuves.create');
    Route::post('/gestion/epreuves', [EpreuveController::class, 'store'])->name('epreuves.store');

    // Création d'un lieu à la volée depuis le formulaire de création d'épreuve
    Route::post('/gestion/lieux', [LieuController::class, 'store'])->name('lieux.store');

    // Pages GEST : gestion des épreuves (session 2/3 : "Affecter les jurys")
    // {epreuve} dans l'URL est résolu automatiquement en modèle Epreuve
    // par Laravel (route model binding), à partir de son id.
    Route::get('/gestion/epreuves/{epreuve}/affecter', [AffectationController::class, 'create'])->name('epreuves.affecter');
    Route::post('/gestion/epreuves/{epreuve}/affecter', [AffectationController::class, 'store'])->name('epreuves.affecter.store');

    // Pages GEST : gestion des épreuves (session 3/3 : "Envoyer les convocations")
    // Nom de route au PLURIEL ('epreuves.convocations') pour bien le
    // distinguer de la route candidat 'convocation' (singulier) ci-dessus :
    // ce sont deux pages très différentes malgré le nom proche.
    Route::get('/gestion/epreuves/{epreuve}/convocations', [EpreuveController::class, 'convocations'])->name('epreuves.convocations');

    // Pages ADMIN : gestion des utilisateurs (étape 1/2)
    // On utilise POST partout (même pour la modification), comme pour le
    // reste de cette appli : pas de @method('PUT') à expliquer en plus,
    // ça reste plus simple pour un formulaire HTML classique.
    Route::get('/admin/utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
    Route::get('/admin/utilisateurs/creer', [UtilisateurController::class, 'create'])->name('utilisateurs.create');
    Route::post('/admin/utilisateurs', [UtilisateurController::class, 'store'])->name('utilisateurs.store');
    Route::get('/admin/utilisateurs/{utilisateur}/modifier', [UtilisateurController::class, 'edit'])->name('utilisateurs.edit');
    Route::post('/admin/utilisateurs/{utilisateur}/modifier', [UtilisateurController::class, 'update'])->name('utilisateurs.update');
});