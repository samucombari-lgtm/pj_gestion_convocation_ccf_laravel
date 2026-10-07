<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

// Réponse renvoyée par Fortify juste APRÈS une inscription réussie.
//
// Pourquoi une réponse personnalisée ? Dans le code de Fortify
// (RegisteredUserController), le nouvel utilisateur est CONNECTÉ
// automatiquement dès que son compte est créé. Or, chez nous, un compte
// inscrit est Inactif (statut 'I') tant qu'un administrateur ne l'a pas
// validé : il ne doit donc pas rester connecté.
//
// Cette classe remplace la réponse par défaut de Fortify (le "branchement"
// est fait dans FortifyServiceProvider::register()).
class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // 1) On annule la connexion que Fortify vient d'ouvrir.
        Auth::guard('web')->logout();

        // 2) On détruit la session et on régénère le jeton CSRF, comme lors
        //    d'une vraie déconnexion (aucune trace de la connexion ne reste).
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 3) Retour à la page de connexion avec un message. with('status', ...)
        //    met le message en session pour UNE seule page : la vue
        //    auth/login.blade.php l'affiche avec session('status').
        return redirect()->route('login')
            ->with('status', 'Compte créé, en attente de validation par un administrateur.');
    }
}
