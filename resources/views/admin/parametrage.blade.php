<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paramétrage</title>
</head>
<body>
    <h1>Paramétrage (rôles, statuts, genres)</h1>

    <p><a href="{{ route('menu') }}">&larr; Retour au menu</a></p>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    {{-- Pas de bouton "Supprimer" : ces valeurs sont utilisées par les
         fiches utilisateurs (clés étrangères). On ne fait qu'AJOUTER. --}}

    {{-- ===================== Rôles ===================== --}}
    <h2>Rôles</h2>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr><th>Code</th><th>Nom</th><th>Commentaire</th></tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->code }}</td>
                    <td>{{ $role->nom }}</td>
                    <td>{{ $role->commentaire ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Ajouter un rôle</h3>
    <form method="POST" action="{{ route('parametrage.roles.store') }}">
        @csrf
        <p>
            <label for="role_code">Code (10 caractères max, ex. SECR) :</label><br>
            <input type="text" id="role_code" name="role_code" maxlength="10" value="{{ old('role_code') }}">
            @if ($errors->first('role_code'))
                <br><span style="color: red;">{{ $errors->first('role_code') }}</span>
            @endif
        </p>
        <p>
            <label for="role_nom">Nom :</label><br>
            <input type="text" id="role_nom" name="role_nom" maxlength="100" value="{{ old('role_nom') }}">
            @if ($errors->first('role_nom'))
                <br><span style="color: red;">{{ $errors->first('role_nom') }}</span>
            @endif
        </p>
        <p>
            <label for="role_commentaire">Commentaire (facultatif) :</label><br>
            <input type="text" id="role_commentaire" name="role_commentaire" maxlength="250" value="{{ old('role_commentaire') }}">
            @if ($errors->first('role_commentaire'))
                <br><span style="color: red;">{{ $errors->first('role_commentaire') }}</span>
            @endif
        </p>
        <button type="submit">Ajouter le rôle</button>
    </form>

    {{-- ===================== Statuts ===================== --}}
    <h2>Statuts</h2>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr><th>Code</th><th>Nom</th><th>Commentaire</th></tr>
        </thead>
        <tbody>
            @foreach ($statuts as $statut)
                <tr>
                    <td>{{ $statut->code }}</td>
                    <td>{{ $statut->nom }}</td>
                    <td>{{ $statut->commentaire ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Rappel utile pour l'admin : seul le statut "A" (Actif) permet de
         se connecter (voir LoginController::login()). --}}
    <p><em>Attention : seuls les comptes au statut « A » (Actif) peuvent se connecter.
        Un nouveau statut bloquera donc la connexion des comptes qui l'ont.</em></p>

    <h3>Ajouter un statut</h3>
    <form method="POST" action="{{ route('parametrage.statuts.store') }}">
        @csrf
        <p>
            <label for="statut_code">Code (1 lettre) :</label><br>
            <input type="text" id="statut_code" name="statut_code" maxlength="1" size="2" value="{{ old('statut_code') }}">
            @if ($errors->first('statut_code'))
                <br><span style="color: red;">{{ $errors->first('statut_code') }}</span>
            @endif
        </p>
        <p>
            <label for="statut_nom">Nom :</label><br>
            <input type="text" id="statut_nom" name="statut_nom" maxlength="100" value="{{ old('statut_nom') }}">
            @if ($errors->first('statut_nom'))
                <br><span style="color: red;">{{ $errors->first('statut_nom') }}</span>
            @endif
        </p>
        <p>
            <label for="statut_commentaire">Commentaire (facultatif) :</label><br>
            <input type="text" id="statut_commentaire" name="statut_commentaire" maxlength="250" value="{{ old('statut_commentaire') }}">
            @if ($errors->first('statut_commentaire'))
                <br><span style="color: red;">{{ $errors->first('statut_commentaire') }}</span>
            @endif
        </p>
        <button type="submit">Ajouter le statut</button>
    </form>

    {{-- ===================== Genres ===================== --}}
    <h2>Genres</h2>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr><th>Code</th><th>Nom</th><th>Commentaire</th></tr>
        </thead>
        <tbody>
            @foreach ($genres as $genre)
                <tr>
                    <td>{{ $genre->code }}</td>
                    <td>{{ $genre->nom }}</td>
                    <td>{{ $genre->commentaire ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Ajouter un genre</h3>
    <form method="POST" action="{{ route('parametrage.genres.store') }}">
        @csrf
        <p>
            <label for="genre_code">Code (1 lettre) :</label><br>
            <input type="text" id="genre_code" name="genre_code" maxlength="1" size="2" value="{{ old('genre_code') }}">
            @if ($errors->first('genre_code'))
                <br><span style="color: red;">{{ $errors->first('genre_code') }}</span>
            @endif
        </p>
        <p>
            <label for="genre_nom">Nom :</label><br>
            <input type="text" id="genre_nom" name="genre_nom" maxlength="100" value="{{ old('genre_nom') }}">
            @if ($errors->first('genre_nom'))
                <br><span style="color: red;">{{ $errors->first('genre_nom') }}</span>
            @endif
        </p>
        <p>
            <label for="genre_commentaire">Commentaire (facultatif) :</label><br>
            <input type="text" id="genre_commentaire" name="genre_commentaire" maxlength="250" value="{{ old('genre_commentaire') }}">
            @if ($errors->first('genre_commentaire'))
                <br><span style="color: red;">{{ $errors->first('genre_commentaire') }}</span>
            @endif
        </p>
        <button type="submit">Ajouter le genre</button>
    </form>
</body>
</html>
