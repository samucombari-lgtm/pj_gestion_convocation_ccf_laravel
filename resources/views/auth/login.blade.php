@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
    <h1>Connexion</h1>

    {{-- Message d'information envoyé avec ->with('status', ...), par
         exemple après une inscription ("Compte créé, en attente de
         validation par un administrateur") ou après un nouveau mot de
         passe ("Votre mot de passe a été réinitialisé."). --}}
    @if (session('status'))
        <p class="alert alert-success">{{ session('status') }}</p>
    @endif

    {{-- $errors est une variable que Laravel remplit automatiquement en
         cas d'échec ("Identifiants incorrects.", "Ce compte est
         désactivé.", "Ce compte est bloqué.", trop de tentatives...).
         S'il n'y a pas d'erreur, ce bloc ne s'affiche simplement pas. --}}
    @if ($errors->any())
        <p class="alert alert-error">{{ $errors->first() }}</p>
    @endif

    {{-- Structure commune des formulaires (partie 9 de style.css). --}}
    <form method="POST" action="{{ route('login') }}" class="form">
        {{-- @csrf génère un champ caché avec un jeton de sécurité.
             Laravel refuse toute requête POST qui ne le contient pas :
             ça empêche un site tiers d'envoyer un faux formulaire à ta place. --}}
        @csrf

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-bloc">Se connecter</button>
        </div>
    </form>

    {{-- Liens secondaires : pages fournies par Fortify. L'inscription n'est
         proposée que si la route existe (Route::has). --}}
    <div class="liens-secondaires">
        <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}">S'inscrire</a>
        @endif
    </div>
@endsection
