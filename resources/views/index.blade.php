@extends('layouts.auth')

@section('title', 'Accueil')

@section('content')
    {{-- Accueil des visiteurs NON connectés (route "accueil", "/").
         Un utilisateur connecté n'arrive jamais ici : HomeController le
         redirige vers /menu. --}}
    <h1>Gestion des convocations aux épreuves E5/E6</h1>

    <p class="remarque">
        Application de gestion des convocations aux épreuves E5 et E6 du
        BTS SIO, évaluées en CCF, au lycée Saint Joseph.
    </p>

    {{-- Les boutons sont de simples liens <a> avec la classe .btn : ils
         ressemblent à des boutons et ouvrent la page indiquée. --}}
    <div class="boutons-accueil">
        <a class="btn btn-primary btn-bloc" href="{{ route('login') }}">Se connecter</a>

        {{-- Route::has('register') renvoie false si l'inscription est
             désactivée dans Fortify : le bouton est alors simplement
             masqué, au lieu de provoquer une erreur
             "Route [register] not defined". --}}
        @if (Route::has('register'))
            <a class="btn btn-bloc" href="{{ route('register') }}">S'inscrire</a>
        @endif
    </div>
@endsection
