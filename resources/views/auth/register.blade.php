@extends('layouts.auth')

@section('title', 'Inscription')

{{-- Carte plus large (520 px) : le formulaire d'inscription est long. --}}
@section('largeur', 'carte-large')

@section('content')
    <h1>Inscription</h1>

    {{-- Cette page est affichée par Fortify (route GET /register), grâce à
         Fortify::registerView() dans FortifyServiceProvider. Le formulaire
         est traité par Fortify (POST /register), qui appelle notre action
         App\Actions\Fortify\CreateNewUser. --}}

    <p class="remarque">
        Votre compte sera créé en tant que candidat. Il devra être validé
        par un administrateur avant que vous puissiez vous connecter.
    </p>

    {{-- En cas d'erreur : message rouge en haut, puis le détail sous
         chaque champ concerné (champ manquant, email déjà utilisé, mots
         de passe différents...). --}}
    @include('partials.erreurs')

    {{-- Structure commune des formulaires (partie 9 de style.css). --}}
    <form method="POST" action="{{ route('register') }}" class="form">
        @csrf

        {{-- old('nom') : remet la valeur déjà tapée si le formulaire revient
             avec une erreur, pour ne pas tout ressaisir. Le mot de passe,
             lui, n'est jamais réaffiché. --}}
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="{{ old('nom') }}">
            @if ($errors->first('nom'))
                <span class="field-error">{{ $errors->first('nom') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}">
            @if ($errors->first('prenom'))
                <span class="field-error">{{ $errors->first('prenom') }}</span>
            @endif
        </div>

        {{-- Liste des genres lue dans la table mcd_genres (envoyée par
             Fortify::registerView dans FortifyServiceProvider). --}}
        <div class="form-group">
            <label for="code_genre">Genre :</label>
            <select id="code_genre" name="code_genre">
                <option value="">-- Choisir --</option>
                @foreach ($genres as $genre)
                    <option value="{{ $genre->code }}" @selected(old('code_genre') === $genre->code)>{{ $genre->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_genre'))
                <span class="field-error">{{ $errors->first('code_genre') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="classe">Classe (facultatif) :</label>
            <input type="text" id="classe" name="classe" value="{{ old('classe') }}">
            @if ($errors->first('classe'))
                <span class="field-error">{{ $errors->first('classe') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @if ($errors->first('email'))
                <span class="field-error">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="password">Mot de passe (8 caractères minimum) :</label>
            <input type="password" id="password" name="password">
            @if ($errors->first('password'))
                <span class="field-error">{{ $errors->first('password') }}</span>
            @endif
        </div>

        {{-- Le nom "password_confirmation" est imposé par la règle
             "confirmed" : Laravel vérifie que les deux champs sont égaux
             (l'erreur éventuelle est rattachée au champ "password"). --}}
        <div class="form-group">
            <label for="password_confirmation">Confirmer le mot de passe :</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>

        {{-- Aucun champ rôle ni statut : ils sont fixés par le serveur
             (candidat, Inactif), l'utilisateur ne peut pas les choisir. --}}
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-bloc">S'inscrire</button>
        </div>
    </form>

    <div class="liens-secondaires">
        <a href="{{ route('login') }}">Déjà un compte ? Se connecter</a>
    </div>
@endsection
