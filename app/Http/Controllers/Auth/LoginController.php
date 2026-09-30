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
            // Change l'identifiant de session après connexion :
            // protection standard contre le vol de session.
            $request->session()->regenerate();

            // Renvoie vers la page que l'utilisateur voulait voir avant
            // d'être redirigé vers /login (ou /menu par défaut, sinon).
            return redirect()->intended('/menu');
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
