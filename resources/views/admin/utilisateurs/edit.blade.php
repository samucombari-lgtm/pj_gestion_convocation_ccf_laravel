<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier {{ $utilisateur->prenom }} {{ $utilisateur->nom }}</title>
</head>
<body>
    <h1>Modifier un utilisateur</h1>

    <p><a href="{{ route('utilisateurs.index') }}">&larr; Retour à la liste des utilisateurs</a></p>

    {{-- Email affiché en lecture seule, juste comme repère : ce n'est pas
         un champ du formulaire (pas de modification d'email pour
         l'instant, comme demandé). --}}
    <p>Compte : <strong>{{ $utilisateur->compte?->email }}</strong></p>

    <form method="POST" action="{{ route('utilisateurs.update', $utilisateur) }}">
        @csrf

        {{-- old('nom', $utilisateur->nom) : reprend la valeur déjà saisie
             si le formulaire vient d'être renvoyé avec une erreur, sinon
             affiche la valeur actuelle en base - le 2e argument de old()
             est la valeur par défaut. --}}
        <p>
            <label for="nom">Nom :</label><br>
            <input type="text" id="nom" name="nom" value="{{ old('nom', $utilisateur->nom) }}">
            @if ($errors->first('nom'))
                <br><span style="color: red;">{{ $errors->first('nom') }}</span>
            @endif
        </p>

        <p>
            <label for="prenom">Prénom :</label><br>
            <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $utilisateur->prenom) }}">
            @if ($errors->first('prenom'))
                <br><span style="color: red;">{{ $errors->first('prenom') }}</span>
            @endif
        </p>

        <p>
            <label for="classe">Classe (facultatif) :</label><br>
            <input type="text" id="classe" name="classe" value="{{ old('classe', $utilisateur->classe) }}">
        </p>

        <p>
            <label for="numero_candidat">Numéro de candidat (facultatif) :</label><br>
            <input type="text" id="numero_candidat" name="numero_candidat" value="{{ old('numero_candidat', $utilisateur->numero_candidat) }}">
        </p>

        <p>
            <label for="id_role">Rôle :</label><br>
            <select id="id_role" name="id_role">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((int) old('id_role', $utilisateur->id_role) === $role->id)>{{ $role->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('id_role'))
                <br><span style="color: red;">{{ $errors->first('id_role') }}</span>
            @endif
        </p>

        <p>
            <label for="code_statut">Statut :</label><br>
            <select id="code_statut" name="code_statut">
                @foreach ($statuts as $statut)
                    <option value="{{ $statut->code }}" @selected(old('code_statut', $utilisateur->code_statut) === $statut->code)>{{ $statut->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_statut'))
                <br><span style="color: red;">{{ $errors->first('code_statut') }}</span>
            @endif
            @if($utilisateur->code_statut === 'B')
                <br><span style="color: red;">
                    ⚠ Ce compte est actuellement banni. Limite connue : la connexion n'est pas encore bloquée pour un compte banni (à faire dans une étape ultérieure).
                </span>
            @endif
        </p>

        <p>
            <label for="code_genre">Genre :</label><br>
            <select id="code_genre" name="code_genre">
                @foreach ($genres as $genre)
                    <option value="{{ $genre->code }}" @selected(old('code_genre', $utilisateur->code_genre) === $genre->code)>{{ $genre->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_genre'))
                <br><span style="color: red;">{{ $errors->first('code_genre') }}</span>
            @endif
        </p>

        <button type="submit">Enregistrer les modifications</button>
    </form>
</body>
</html>
