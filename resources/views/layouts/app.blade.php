<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    {{-- "viewport" : affichage correct sur téléphone (sans zoom arrière). --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Chaque page fournit son titre avec @section('title', '...').
         Le 2e argument de @yield est le titre par défaut. --}}
    <title>@yield('title', 'Accueil') — Convocations E5/E6</title>

    {{-- Feuille de style unique : public/css/style.css.
         asset() construit l'adresse complète du fichier. --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    {{-- Layout commun à toutes les pages accessibles une fois connecté.
         Une page l'utilise avec @extends('layouts.app') et place son
         contenu entre @section('content') et @endsection. --}}

    {{-- En-tête : barre de menu selon le rôle (fichier séparé). --}}
    @include('partials.menu')

    {{-- Zone de contenu centrée : @yield('content') est remplacé par le
         contenu de la page. @yield('largeur') ajoute une classe en plus si
         la page le demande, par exemple @section('largeur', 'contenu-large')
         pour un grand tableau (sinon : rien, largeur normale). --}}
    <main class="contenu @yield('largeur')">
        @yield('content')
    </main>

    <footer class="pied">
        Gestion des convocations aux épreuves E5/E6 — BTS SIO — Lycée Saint Joseph — {{ date('Y') }}
    </footer>

    {{-- Remarque : Livewire ajoute tout seul son script juste avant
         </body> sur les pages qui contiennent un composant (gestion des
         comptes). Rien à écrire ici. --}}
</body>
</html>
