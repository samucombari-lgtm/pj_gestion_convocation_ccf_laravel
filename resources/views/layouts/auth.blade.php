<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    {{-- "viewport" : affichage correct sur téléphone (sans zoom arrière). --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Chaque page fournit son titre avec @section('title', '...'). --}}
    <title>@yield('title', 'Accueil') — Convocations E5/E6</title>

    {{-- Même feuille de style que le layout "app" (public/css/style.css) :
         même police et mêmes couleurs partout. --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="page-auth">
    {{-- Layout des pages PUBLIQUES (visiteur non connecté) : accueil,
         connexion, inscription, mot de passe oublié, nouveau mot de passe.
         Pas de barre de menu ici (personne n'est connecté) : juste une
         carte blanche centrée sur un fond clair.
         Une page l'utilise avec @extends('layouts.auth') et place son
         contenu entre @section('content') et @endsection.
         @yield('largeur') : classe en plus si la page veut une carte plus
         large, par exemple @section('largeur', 'carte-large') pour
         l'inscription (sinon : largeur normale). --}}
    <main class="carte-auth @yield('largeur')">
        {{-- Nom de l'application en haut de la carte. --}}
        <p class="marque-auth">Convocations E5/E6</p>

        @yield('content')
    </main>

    <footer class="pied">
        Gestion des convocations aux épreuves E5/E6 — BTS SIO — Lycée Saint Joseph — {{ date('Y') }}
    </footer>
</body>
</html>
