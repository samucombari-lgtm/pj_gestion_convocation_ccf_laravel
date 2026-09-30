<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une session d'épreuves</title>
</head>
<body>
    <h1>Créer une session d'épreuves</h1>

    <p><a href="{{ route('epreuves.index') }}">&larr; Retour à la liste des épreuves</a></p>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    {{-- ===================== Formulaire épreuve ===================== --}}
    <form method="POST" action="{{ route('epreuves.store') }}">
        @csrf

        <p>
            <label for="code">Code (ex. E5S1) :</label><br>
            <input type="text" id="code" name="code" value="{{ old('code') }}">
            {{-- $errors->first('code') renvoie le 1er message de validation
                 pour ce champ, ou une chaîne vide s'il n'y en a pas. --}}
            @if ($errors->first('code'))
                <br><span style="color: red;">{{ $errors->first('code') }}</span>
            @endif
        </p>

        <p>
            <label for="nom">Nom de l'épreuve :</label><br>
            <input type="text" id="nom" name="nom" value="{{ old('nom') }}">
            @if ($errors->first('nom'))
                <br><span style="color: red;">{{ $errors->first('nom') }}</span>
            @endif
        </p>

        <p>
            <label for="date_debut">Date et heure de début :</label><br>
            {{-- type="datetime-local" affiche un vrai sélecteur de date +
                 heure dans le navigateur, sans JavaScript à écrire. --}}
            <input type="datetime-local" id="date_debut" name="date_debut" value="{{ old('date_debut') }}">
            @if ($errors->first('date_debut'))
                <br><span style="color: red;">{{ $errors->first('date_debut') }}</span>
            @endif
        </p>

        <p>
            <label for="date_fin">Date et heure de fin :</label><br>
            <input type="datetime-local" id="date_fin" name="date_fin" value="{{ old('date_fin') }}">
            @if ($errors->first('date_fin'))
                <br><span style="color: red;">{{ $errors->first('date_fin') }}</span>
            @endif
        </p>

        <p>
            <label for="duree_candidat">Durée de passage par candidat (en minutes) :</label><br>
            <input type="number" id="duree_candidat" name="duree_candidat" min="1" value="{{ old('duree_candidat') }}">
            @if ($errors->first('duree_candidat'))
                <br><span style="color: red;">{{ $errors->first('duree_candidat') }}</span>
            @endif
        </p>

        <p>
            <label for="id_lieu">Lieu :</label><br>
            <select id="id_lieu" name="id_lieu">
                <option value="">-- Choisir un lieu --</option>
                @foreach ($lieux as $lieu)
                    {{-- selected si ce lieu est celui choisi avant une
                         erreur de validation (redisplay), ou si c'est le
                         lieu qu'on vient de créer avec le formulaire
                         ci-dessous (voir LieuController::store()). --}}
                    <option value="{{ $lieu->id }}" @selected((int) old('id_lieu') === $lieu->id)>
                        {{ $lieu->nom }} ({{ $lieu->code }})
                    </option>
                @endforeach
            </select>
            @if ($errors->first('id_lieu'))
                <br><span style="color: red;">{{ $errors->first('id_lieu') }}</span>
            @endif
        </p>

        <button type="submit">Créer l'épreuve</button>
    </form>

    {{-- ===================== Formulaire nouveau lieu ===================== --}}
    {{-- <details>/<summary> est un élément HTML natif qui s'ouvre/se ferme
         au clic, sans une seule ligne de JavaScript : exactement ce qu'il
         faut pour "un lien qui ouvre un petit formulaire séparé". On le
         laisse ouvert automatiquement (attribut "open") si une erreur de
         validation concerne un champ "lieu_*", pour que l'utilisateur
         voie tout de suite pourquoi ça a échoué. --}}
    <details @if ($errors->first('lieu_code') || $errors->first('lieu_nom')) open @endif>
        <summary>+ Ajouter un nouveau lieu</summary>

        <form method="POST" action="{{ route('lieux.store') }}">
            @csrf

            <p>
                <label for="lieu_code">Code du lieu (ex. SALLE104) :</label><br>
                <input type="text" id="lieu_code" name="lieu_code" value="{{ old('lieu_code') }}">
                @if ($errors->first('lieu_code'))
                    <br><span style="color: red;">{{ $errors->first('lieu_code') }}</span>
                @endif
            </p>

            <p>
                <label for="lieu_nom">Nom du lieu :</label><br>
                <input type="text" id="lieu_nom" name="lieu_nom" value="{{ old('lieu_nom') }}">
                @if ($errors->first('lieu_nom'))
                    <br><span style="color: red;">{{ $errors->first('lieu_nom') }}</span>
                @endif
            </p>

            <p>
                <label for="lieu_adresse">Adresse (facultatif) :</label><br>
                <input type="text" id="lieu_adresse" name="lieu_adresse" value="{{ old('lieu_adresse') }}">
            </p>

            <button type="submit">Créer ce lieu</button>
        </form>
    </details>
</body>
</html>
