<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>

    {{-- Cette page est affichée par Fortify (route GET /register), grâce à
         Fortify::registerView() dans FortifyServiceProvider. Le formulaire
         est traité par Fortify (POST /register), qui appelle notre action
         App\Actions\Fortify\CreateNewUser. --}}

    <p>
        Votre compte sera créé en tant que candidat. Il devra être validé
        par un administrateur avant que vous puissiez vous connecter.
    </p>

    {{-- En cas d'erreur, on affiche toutes les erreurs de validation
         (champ manquant, email déjà utilisé, mots de passe différents...). --}}
    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- old('nom') : remet la valeur déjà tapée si le formulaire revient
             avec une erreur, pour ne pas tout ressaisir. Le mot de passe,
             lui, n'est jamais réaffiché. --}}
        <label>
            Nom :
            <input type="text" name="nom" value="{{ old('nom') }}">
        </label>
        <br>

        <label>
            Prénom :
            <input type="text" name="prenom" value="{{ old('prenom') }}">
        </label>
        <br>

        {{-- Liste des genres lue dans la table mcd_genres (envoyée par
             Fortify::registerView dans FortifyServiceProvider). --}}
        <label>
            Genre :
            <select name="code_genre">
                <option value="">-- Choisir --</option>
                @foreach ($genres as $genre)
                    <option value="{{ $genre->code }}" @selected(old('code_genre') === $genre->code)>{{ $genre->nom }}</option>
                @endforeach
            </select>
        </label>
        <br>

        <label>
            Classe (facultatif) :
            <input type="text" name="classe" value="{{ old('classe') }}">
        </label>
        <br>

        <label>
            Email :
            <input type="email" name="email" value="{{ old('email') }}">
        </label>
        <br>

        <label>
            Mot de passe (8 caractères minimum) :
            <input type="password" name="password">
        </label>
        <br>

        {{-- Le nom "password_confirmation" est imposé par la règle
             "confirmed" : Laravel vérifie que les deux champs sont égaux. --}}
        <label>
            Confirmer le mot de passe :
            <input type="password" name="password_confirmation">
        </label>
        <br>

        {{-- Aucun champ rôle ni statut : ils sont fixés par le serveur
             (candidat, Inactif), l'utilisateur ne peut pas les choisir. --}}
        <button type="submit">S'inscrire</button>
    </form>

    <p><a href="{{ route('login') }}">Déjà un compte ? Se connecter</a></p>
</body>
</html>
