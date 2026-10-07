@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
    <h1>Mot de passe oublié</h1>

    {{-- Page affichée par Fortify (route GET /forgot-password), grâce à
         Fortify::requestPasswordResetLinkView() dans FortifyServiceProvider.
         Le formulaire est traité par Fortify (POST /forgot-password), qui
         envoie un e-mail contenant un lien de réinitialisation. --}}

    <p class="remarque">
        Saisissez l'adresse e-mail de votre compte : vous recevrez un lien
        pour choisir un nouveau mot de passe.
    </p>

    {{-- Message de réussite ("Nous vous avons envoyé par e-mail le
         lien..."), qui vient de lang/fr/passwords.php. --}}
    @if (session('status'))
        <p class="alert alert-success">{{ session('status') }}</p>
    @endif

    {{-- Structure commune des formulaires (partie 9 de style.css). --}}
    <form method="POST" action="{{ route('password.email') }}" class="form">
        @csrf

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            {{-- Message d'erreur sous le champ (email vide ou inconnu,
                 demande trop rapprochée...). --}}
            @if ($errors->first('email'))
                <span class="field-error">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-bloc">Recevoir le lien</button>
        </div>
    </form>

    <div class="liens-secondaires">
        <a href="{{ route('login') }}">Retour à la connexion</a>
    </div>
@endsection
