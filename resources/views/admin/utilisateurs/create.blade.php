<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un utilisateur</title>
</head>
<body>
    <h1>Créer un utilisateur</h1>

    <p><a href="{{ route('utilisateurs.index') }}">&larr; Retour à la liste des utilisateurs</a></p>

    <form method="POST" action="{{ route('utilisateurs.store') }}">
        @csrf

        <p>
            <label for="nom">Nom :</label><br>
            <input type="text" id="nom" name="nom" value="{{ old('nom') }}">
            @if ($errors->first('nom'))
                <br><span style="color: red;">{{ $errors->first('nom') }}</span>
            @endif
        </p>

        <p>
            <label for="prenom">Prénom :</label><br>
            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}">
            @if ($errors->first('prenom'))
                <br><span style="color: red;">{{ $errors->first('prenom') }}</span>
            @endif
        </p>

        <p>
            <label for="classe">Classe (facultatif, pour un candidat) :</label><br>
            <input type="text" id="classe" name="classe" value="{{ old('classe') }}">
        </p>

        <p>
            <label for="numero_candidat">Numéro de candidat (facultatif) :</label><br>
            <input type="text" id="numero_candidat" name="numero_candidat" value="{{ old('numero_candidat') }}">
        </p>

        <p>
            <label for="email">Email :</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @if ($errors->first('email'))
                <br><span style="color: red;">{{ $errors->first('email') }}</span>
            @endif
        </p>

        <p>
            <label for="password">Mot de passe (au moins 8 caractères) :</label><br>
            {{-- Champ texte (pas "password") pour que l'admin voie
                 exactement ce qu'il saisit avant de le transmettre à
                 l'utilisateur - il sera haché automatiquement à
                 l'enregistrement (voir UtilisateurController::store()). --}}
            <input type="text" id="password" name="password">
            @if ($errors->first('password'))
                <br><span style="color: red;">{{ $errors->first('password') }}</span>
            @endif
        </p>

        <p>
            <label for="id_role">Rôle :</label><br>
            <select id="id_role" name="id_role">
                <option value="">-- Choisir un rôle --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((int) old('id_role') === $role->id)>{{ $role->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('id_role'))
                <br><span style="color: red;">{{ $errors->first('id_role') }}</span>
            @endif
        </p>

        <p>
            <label for="code_statut">Statut :</label><br>
            <select id="code_statut" name="code_statut">
                <option value="">-- Choisir un statut --</option>
                @foreach ($statuts as $statut)
                    <option value="{{ $statut->code }}" @selected(old('code_statut') === $statut->code)>{{ $statut->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_statut'))
                <br><span style="color: red;">{{ $errors->first('code_statut') }}</span>
            @endif
        </p>

        <p>
            <label for="code_genre">Genre :</label><br>
            <select id="code_genre" name="code_genre">
                <option value="">-- Choisir un genre --</option>
                @foreach ($genres as $genre)
                    <option value="{{ $genre->code }}" @selected(old('code_genre') === $genre->code)>{{ $genre->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_genre'))
                <br><span style="color: red;">{{ $errors->first('code_genre') }}</span>
            @endif
        </p>

        <button type="submit">Créer l'utilisateur</button>
    </form>
</body>
</html>
