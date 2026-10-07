@extends('layouts.app')

@section('title')
    Modifier {{ $utilisateur->prenom }} {{ $utilisateur->nom }}
@endsection

@section('content')
    <h1>Modifier un utilisateur</h1>

    <p><a class="btn" href="{{ route('utilisateurs.index') }}">&larr; Retour à la liste des utilisateurs</a></p>

    @include('partials.erreurs')

    {{-- Email affiché en lecture seule, juste comme repère : ce n'est pas
         un champ du formulaire (pas de modification d'email pour
         l'instant, comme demandé). --}}
    <p>Compte : <strong>{{ $utilisateur->compte?->email }}</strong></p>

    {{-- Structure commune à tous les formulaires (voir la partie 9 de
         style.css) : class="form", un div.form-group par champ, et les
         boutons dans div.form-actions. --}}
    <form method="POST" action="{{ route('utilisateurs.update', $utilisateur) }}" class="form">
        @csrf

        {{-- old('nom', $utilisateur->nom) : reprend la valeur déjà saisie
             si le formulaire vient d'être renvoyé avec une erreur, sinon
             affiche la valeur actuelle en base - le 2e argument de old()
             est la valeur par défaut. --}}
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="{{ old('nom', $utilisateur->nom) }}">
            @if ($errors->first('nom'))
                <span class="field-error">{{ $errors->first('nom') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $utilisateur->prenom) }}">
            @if ($errors->first('prenom'))
                <span class="field-error">{{ $errors->first('prenom') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="classe">Classe (facultatif) :</label>
            <input type="text" id="classe" name="classe" value="{{ old('classe', $utilisateur->classe) }}">
            @if ($errors->first('classe'))
                <span class="field-error">{{ $errors->first('classe') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="numero_candidat">Numéro de candidat (facultatif) :</label>
            <input type="text" id="numero_candidat" name="numero_candidat" value="{{ old('numero_candidat', $utilisateur->numero_candidat) }}">
            @if ($errors->first('numero_candidat'))
                <span class="field-error">{{ $errors->first('numero_candidat') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="id_role">Rôle :</label>
            <select id="id_role" name="id_role">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((int) old('id_role', $utilisateur->id_role) === $role->id)>{{ $role->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('id_role'))
                <span class="field-error">{{ $errors->first('id_role') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="code_statut">Statut :</label>
            <select id="code_statut" name="code_statut">
                @foreach ($statuts as $statut)
                    <option value="{{ $statut->code }}" @selected(old('code_statut', $utilisateur->code_statut) === $statut->code)>{{ $statut->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_statut'))
                <span class="field-error">{{ $errors->first('code_statut') }}</span>
            @endif
            @if($utilisateur->code_statut === 'B')
                <span class="field-error">
                    ⚠ Ce compte est actuellement banni : sa connexion est refusée avec le message « Ce compte est bloqué. ».
                </span>
            @endif
        </div>

        <div class="form-group">
            <label for="code_genre">Genre :</label>
            <select id="code_genre" name="code_genre">
                @foreach ($genres as $genre)
                    <option value="{{ $genre->code }}" @selected(old('code_genre', $utilisateur->code_genre) === $genre->code)>{{ $genre->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_genre'))
                <span class="field-error">{{ $errors->first('code_genre') }}</span>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </div>
    </form>
@endsection
