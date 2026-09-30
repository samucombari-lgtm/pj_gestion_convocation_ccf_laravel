<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Appelée quand on visite /login en GET (juste pour AFFICHER le formulaire)
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Appelée quand le formulaire est ENVOYÉ (en POST)
    public function login(Request $request)
    {
        // $request->validate() vérifie que les champs sont bien remplis
        // et au bon format. Si ce n'est pas le cas, Laravel renvoie
        // automatiquement en arrière avec un message d'erreur, sans
        // qu'on ait à écrire ce cas nous-mêmes.
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Auth::attempt() va chercher dans mcd_users (via le Model User)
        // la ligne qui correspond à cet email, puis compare le mot de
        // passe envoyé avec le hash bcrypt stocké en base.
        // Il renvoie true/false, on ne compare JAMAIS de mot de passe
        // nous-mêmes à la main.
        if (Auth::attempt($credentials)) {
            // Auth::attempt() ne vérifie QUE l'email et le mot de passe :
            // il ne sait rien du statut (Actif / Inactif / Banni), qui est
            // stocké dans mcd_utilisateurs. On le vérifie donc nous-mêmes,
            // juste après la connexion.
            //
            // Choix : seul le statut 'A' (Actif) peut se connecter. On bloque
            // donc 'B' (Banni) ET 'I' (Inactif), car :
            //   - la base elle-même le dit : le commentaire du statut 'I'
            //     dans mcd_statuts est "Compte désactivé temporairement,
            //     ne peut plus se connecter" ;
            //   - on liste ce qui est AUTORISÉ plutôt que ce qui est interdit :
            //     si un admin ajoute un nouveau statut depuis la page
            //     Paramétrage (ex. 'S' Suspendu), il est bloqué par défaut,
            //     au lieu de laisser passer quelqu'un par oubli.
            //
            // Le "?->" évite une erreur si le compte n'a pas de fiche dans
            // mcd_utilisateurs : $code vaut alors null, donc différent de
            // 'A', et la connexion est refusée (un tel compte n'a de toute
            // façon aucun rôle, il ne pourrait rien faire).
            $code = auth()->user()->utilisateur?->statut?->code;

            if ($code !== 'A') {
                // On annule la connexion qui vient d'être ouverte.
                Auth::logout();

                // Message différent selon le cas, pour que la personne
                // sache s'il s'agit d'un blocage définitif ou temporaire.
                $message = $code === 'I'
                    ? 'Ce compte est désactivé.'
                    : 'Ce compte est bloqué.';

                // onlyInput('email') : on remet l'email dans le formulaire
                // (mais jamais le mot de passe).
                return redirect('/login')
                    ->withErrors(['email' => $message])
                    ->onlyInput('email');
            }

            // Change l'identifiant de session après connexion :
            // protection standard contre le vol de session.
            $request->session()->regenerate();

            // Renvoie vers la page que l'utilisateur voulait voir avant
            // d'être redirigé vers /login (ou /menu par défaut, sinon).
            return redirect('/menu');
        }

        // Si Auth::attempt a échoué : on revient au formulaire avec un message
        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
