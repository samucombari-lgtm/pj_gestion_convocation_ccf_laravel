{{-- Page d'accueil de l'utilisateur connecté (route "menu").
     Elle utilise le layout commun : la barre de menu et le pied de page
     viennent de layouts/app.blade.php. Ici, on ne met que le contenu. --}}
@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    {{-- auth()->user() renvoie l'utilisateur connecté (le modèle User). --}}
    <h1>Bonjour {{ auth()->user()->name }}</h1>
    <p class="remarque">Que souhaitez-vous faire ?</p>

    {{-- Raccourcis en cartes. Chaque carte n'est affichée QUE si
         l'utilisateur a l'action correspondante (@can, voir l'explication
         en haut de partials/menu.blade.php) : mêmes liens que la barre
         de menu. --}}
    <div class="raccourcis">
        @can('utilisateurs.viewAny')
            <a class="card" href="{{ route('utilisateurs.index') }}">
                <strong>Gestion des comptes</strong>
                Rechercher, activer, désactiver ou bannir un compte, changer un rôle.
            </a>
        @endcan
        @can('parametrage.viewAny')
            <a class="card" href="{{ route('parametrage.index') }}">
                <strong>Paramétrage</strong>
                Rôles, statuts et genres.
            </a>
        @endcan

        @can('epreuves.viewAny')
            <a class="card" href="{{ route('epreuves.index') }}">
                <strong>Liste des épreuves</strong>
                Affecter un jury et voir les convocations de chaque épreuve.
            </a>
        @endcan
        @can('epreuves.create')
            <a class="card" href="{{ route('epreuves.create') }}">
                <strong>Créer une session d'épreuves</strong>
                Nouvelle épreuve, avec ajout d'un lieu si besoin.
            </a>
        @endcan

        @can('convocations.view')
            <a class="card" href="{{ route('convocation') }}">
                <strong>Ma convocation</strong>
                Date, heure, lieu et jury de chacune de vos épreuves.
            </a>
        @endcan

        @can('planning.viewAny')
            <a class="card" href="{{ route('jury.planning') }}">
                <strong>Mon planning</strong>
                Les épreuves pour lesquelles vous êtes membre d'un jury.
            </a>
        @endcan
        @can('evaluations.viewAny')
            <a class="card" href="{{ route('jury.candidats') }}">
                <strong>Mes candidats à évaluer</strong>
                Horaire, candidat et épreuve de chaque passage.
            </a>
        @endcan
    </div>
@endsection
