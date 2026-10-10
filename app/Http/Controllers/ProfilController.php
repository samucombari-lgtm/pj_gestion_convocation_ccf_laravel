<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Page "Mon profil", accessible à tous les rôles depuis le menu "Profil".
// Ce contrôleur ne fait qu'AFFICHER : il ne modifie rien. Le changement du
// mot de passe est traité par Fortify (PUT /user/password), avec notre
// action app/Actions/Fortify/UpdateUserPassword.php.
class ProfilController extends Controller
{
    // GET /profil (route "profil", protégée par le middleware auth).
    public function show(Request $request)
    {
        // $request->user() = le compte connecté (mcd_users).
        $user = $request->user();

        // Fiche métier (mcd_utilisateurs) avec son rôle, son statut et son
        // genre : load() les charge en une fois, au lieu de faire une
        // requête à chaque fois que la vue lit ->role, ->statut ou ->genre.
        // Le "?->" évite une erreur si le compte n'a pas de fiche.
        $utilisateur = $user->utilisateur?->load('role', 'statut', 'genre');

        return view('profil.show', [
            'user' => $user,
            'utilisateur' => $utilisateur,
        ]);
    }
}
