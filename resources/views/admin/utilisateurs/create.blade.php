@extends('layouts.app')

@section('title', 'Créer un utilisateur')

@section('content')
    <h1>Créer un utilisateur</h1>

    <p><a class="btn" href="{{ route('utilisateurs.index') }}">&larr; Retour à la liste des utilisateurs</a></p>

    @include('partials.erreurs')

    {{-- Structure commune à tous les formulaires (voir la partie 9 de
         style.css) : class="form", un div.form-group par champ, et les
         boutons dans div.form-actions. --}}
    <form method="POST" action="{{ route('utilisateurs.store') }}" class="form">
        @csrf

        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="{{ old('nom') }}">
            @if ($errors->first('nom'))
                <span class="field-error">{{ $errors->first('nom') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}">
            @if ($errors->first('prenom'))
                <span class="field-error">{{ $errors->first('prenom') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="classe">Classe (facultatif, pour un candidat) :</label>
            <input type="text" id="classe" name="classe" value="{{ old('classe') }}">
            @if ($errors->first('classe'))
                <span class="field-error">{{ $errors->first('classe') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="numero_candidat">Numéro de candidat (facultatif) :</label>
            <input type="text" id="numero_candidat" name="numero_candidat" value="{{ old('numero_candidat') }}">
            @if ($errors->first('numero_candidat'))
                <span class="field-error">{{ $errors->first('numero_candidat') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @if ($errors->first('email'))
                <span class="field-error">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="password">Mot de passe (au moins 8 caractères) :</label>
            {{-- Champ texte (pas "password") pour que l'admin voie
                 exactement ce qu'il saisit avant de le transmettre à
                 l'utilisateur - il sera haché automatiquement à
                 l'enregistrement (voir UtilisateurController::store()). --}}
            <input type="text" id="password" name="password">
            @if ($errors->first('password'))
                <span class="field-error">{{ $errors->first('password') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="id_role">Rôle :</label>
            <select id="id_role" name="id_role">
                <option value="">-- Choisir un rôle --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((int) old('id_role') === $role->id)>{{ $role->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('id_role'))
                <span class="field-error">{{ $errors->first('id_role') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="code_statut">Statut :</label>
            <select id="code_statut" name="code_statut">
                <option value="">-- Choisir un statut --</option>
                @foreach ($statuts as $statut)
                    <option value="{{ $statut->code }}" @selected(old('code_statut') === $statut->code)>{{ $statut->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_statut'))
                <span class="field-error">{{ $errors->first('code_statut') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="code_genre">Genre :</label>
            <select id="code_genre" name="code_genre">
                <option value="">-- Choisir un genre --</option>
                @foreach ($genres as $genre)
                    <option value="{{ $genre->code }}" @selected(old('code_genre') === $genre->code)>{{ $genre->nom }}</option>
                @endforeach
            </select>
            @if ($errors->first('code_genre'))
                <span class="field-error">{{ $errors->first('code_genre') }}</span>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
        </div>
    </form>
@endsection
