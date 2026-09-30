<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>

    {{-- $errors est une variable que Laravel remplit automatiquement
         quand back()->withErrors([...]) a été appelé dans le Controller.
         S'il n'y a pas d'erreur, ce bloc ne s'affiche simplement pas. --}}
    @if ($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
        {{-- @csrf génère un champ caché avec un jeton de sécurité.
             Laravel refuse toute requête POST qui ne le contient pas :
             ça empêche un site tiers d'envoyer un faux formulaire à ta place. --}}
        @csrf

        <label>
            Email :
            <input type="email" name="email" value="{{ old('email') }}">
        </label>
        <br>

        <label>
            Mot de passe :
            <input type="password" name="password">
        </label>
        <br>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
