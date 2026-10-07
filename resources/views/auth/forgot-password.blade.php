<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
</head>
<body>
    <h1>Mot de passe oublié</h1>

    {{-- Page affichée par Fortify (route GET /forgot-password), grâce à
         Fortify::requestPasswordResetLinkView() dans FortifyServiceProvider.
         Le formulaire est traité par Fortify (POST /forgot-password), qui
         envoie un e-mail contenant un lien de réinitialisation. --}}

    <p>
        Saisissez l'adresse e-mail de votre compte : vous recevrez un lien
        pour choisir un nouveau mot de passe.
    </p>

    {{-- Message de réussite ("Nous vous avons envoyé par e-mail le
         lien..."), qui vient de lang/fr/passwords.php. --}}
    @if (session('status'))
        <p style="color:green">{{ session('status') }}</p>
    @endif

    {{-- Message d'erreur (email inconnu, demande trop rapprochée...). --}}
    @if ($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label>
            Email :
            <input type="email" name="email" value="{{ old('email') }}">
        </label>
        <br>

        <button type="submit">Recevoir le lien</button>
    </form>

    <p><a href="{{ route('login') }}">Retour à la connexion</a></p>
</body>
</html>
