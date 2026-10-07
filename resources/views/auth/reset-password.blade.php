<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau mot de passe</title>
</head>
<body>
    <h1>Nouveau mot de passe</h1>

    {{-- Page ouverte depuis le lien reçu par e-mail :
         /reset-password/{jeton}?email=adresse
         Elle est affichée par Fortify grâce à Fortify::resetPasswordView()
         dans FortifyServiceProvider, qui lui transmet la requête ($request)
         pour qu'on puisse y lire le jeton et l'e-mail. --}}

    {{-- Erreurs : lien invalide ou déjà utilisé, mots de passe différents,
         mot de passe trop court... --}}
    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        {{-- Champs cachés : Fortify en a besoin pour retrouver la demande.
             - token : le jeton secret présent dans le lien (dans l'URL) ;
             - email : l'adresse du compte (paramètre ?email= du lien).
             Laravel vérifie que ce jeton correspond bien à cet e-mail dans
             la table password_reset_tokens et qu'il n'a pas expiré. --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

        <p>Compte : <strong>{{ old('email', $request->email) }}</strong></p>

        <label>
            Nouveau mot de passe (8 caractères minimum) :
            <input type="password" name="password">
        </label>
        <br>

        <label>
            Confirmer le mot de passe :
            <input type="password" name="password_confirmation">
        </label>
        <br>

        <button type="submit">Enregistrer le mot de passe</button>
    </form>

    <p><a href="{{ route('login') }}">Retour à la connexion</a></p>
</body>
</html>
