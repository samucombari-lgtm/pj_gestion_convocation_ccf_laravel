<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Http\Responses\RegisterResponse;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;

// Fortify est un "moteur" d'authentification SANS interface : il fournit
// les routes et les contrôleurs (/login, /logout, /register, mot de passe
// oublié...). Ce provider lui dit, pour NOTRE projet :
//  - quelles vues Blade afficher ;
//  - comment vérifier une connexion (avec notre contrôle du statut) ;
//  - comment créer un compte et quoi faire après l'inscription.
// Il est enregistré à la main dans bootstrap/providers.php.
class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Après une inscription, Fortify utilise NOTRE RegisterResponse
        // (déconnexion immédiate + message) au lieu de la sienne (qui
        // laisserait le nouveau compte Inactif connecté).
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
    }

    public function boot(): void
    {
        // ----- Actions (la logique métier) -----
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // ----- Vues -----
        // Fortify définit la route GET /login (nommée "login") et affiche
        // notre vue existante auth/login.blade.php.
        Fortify::loginView(fn () => view('auth.login'));

        // GET /register (nommée "register") : formulaire d'inscription,
        // avec la liste des genres lue dans mcd_genres.
        Fortify::registerView(fn () => view('auth.register', [
            'genres' => Genre::orderBy('nom')->get(),
        ]));

        // Fortify enregistre TOUJOURS la route /user/confirm-password
        // ("confirmez votre mot de passe avant une action sensible"), même
        // si on ne s'en sert pas. Sans vue associée, elle plantait (erreur
        // 500) : on répond simplement "page introuvable" (404).
        Fortify::confirmPasswordView(fn () => abort(404));

        // ----- Connexion : même contrôle que l'ancien LoginController -----
        // Fortify appelle cette fonction quand le formulaire de connexion est
        // envoyé. Elle doit renvoyer l'utilisateur si la connexion est
        // acceptée, ou null si les identifiants sont faux.
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            // Email inconnu ou mot de passe faux : on renvoie null. Fortify
            // affiche alors le message "auth.failed" de lang/fr/auth.php
            // ("Identifiants incorrects.") ET compte la tentative ratée pour
            // la limitation (5 essais par minute, voir plus bas).
            // Hash::check compare le mot de passe tapé avec le hash en base :
            // on ne compare jamais de mot de passe en clair.
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            // Le mot de passe est bon : on vérifie maintenant le statut
            // (stocké dans mcd_utilisateurs). Seul 'A' (Actif) peut se
            // connecter. On liste ce qui est AUTORISÉ : un futur statut
            // ajouté depuis la page Paramétrage sera bloqué par défaut.
            // Le "?->" évite une erreur si le compte n'a pas de fiche
            // mcd_utilisateurs : $code vaut null, la connexion est refusée.
            $code = $user->utilisateur?->statut?->code;

            if ($code !== 'A') {
                // On lance une erreur de validation sur le champ "email" :
                // Fortify renvoie au formulaire de connexion et la vue
                // l'affiche avec $errors->first(), comme avant.
                throw ValidationException::withMessages([
                    'email' => $code === 'I'
                        ? 'Ce compte est désactivé.'
                        : 'Ce compte est bloqué.',
                ]);
            }

            return $user;
        });

        // ----- Limitation des tentatives de connexion -----
        // Rien à écrire ici : comme 'limiters.login' vaut null dans
        // config/fortify.php, Fortify bloque lui-même un couple
        // email + adresse IP après 5 tentatives ratées en une minute, et
        // affiche le message "auth.throttle" de lang/fr/auth.php.
    }
}
