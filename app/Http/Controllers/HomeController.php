<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    // Page d'accueil publique, reliée à la route GET / (voir routes/web.php).
    public function index()
    {
        // auth()->check() renvoie true si quelqu'un est déjà connecté.
        // Dans ce cas, la page de présentation ne lui sert à rien : on
        // l'envoie directement sur son menu (route nommée "menu").
        if (auth()->check()) {
            return redirect()->route('menu');
        }

        // Sinon (visiteur non connecté) : on affiche la page de présentation
        // avec les boutons "Se connecter" et "S'inscrire".
        return view('index');
    }
}
