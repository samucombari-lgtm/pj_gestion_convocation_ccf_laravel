@extends('layouts.auth')

@section('title', 'Nouveau mot de passe')

@section('content')
    <h1>Nouveau mot de passe</h1>

    {{-- Page ouverte depuis le lien reçu par e-mail :
         /reset-password/{jeton}?email=adresse
         Elle est affichée par Fortify grâce à Fortify::resetPasswordView()
         dans FortifyServiceProvider, qui lui transmet la requête ($request)
         pour qu'on puisse y lire le jeton et l'e-mail. --}}

    {{-- Lien invalide, déjà utilisé ou expiré : Fortify rattache cette
         erreur au champ "email", qui est CACHÉ dans cette page. On
         l'affiche donc en haut de la carte, sinon elle serait invisible. --}}
    @if ($errors->first('email'))
        <p class="alert alert-error">{{ $errors->first('email') }}</p>
    @endif

    {{-- Structure commune des formulaires (partie 9 de style.css). --}}
    <form method="POST" action="{{ route('password.update') }}" class="form">
        @csrf

        {{-- Champs cachés : Fortify en a besoin pour retrouver la demande.
             - token : le jeton secret présent dans le lien (dans l'URL) ;
             - email : l'adresse du compte (paramètre ?email= du lien).
             Laravel vérifie que ce jeton correspond bien à cet e-mail dans
             la table mcd_password_reset_tokens et qu'il n'a pas expiré. --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

        <p>Compte : <strong>{{ old('email', $request->email) }}</strong></p>

        <div class="form-group">
            <label for="password">Nouveau mot de passe (8 caractères minimum) :</label>
            <input type="password" id="password" name="password">
            {{-- Mots de passe différents, trop court... --}}
            @if ($errors->first('password'))
                <span class="field-error">{{ $errors->first('password') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmer le mot de passe :</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-bloc">Enregistrer le mot de passe</button>
        </div>
    </form>

    <div class="liens-secondaires">
        <a href="{{ route('login') }}">Retour à la connexion</a>
    </div>
@endsection
