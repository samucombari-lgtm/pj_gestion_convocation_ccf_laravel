@extends('layouts.app')

@section('title', 'Paramétrage')

@section('content')
    <h1>Paramétrage (rôles, statuts, genres)</h1>

    @if (session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
    @endif

    @include('partials.erreurs')

    {{-- Pas de bouton "Supprimer" : ces valeurs sont utilisées par les
         fiches utilisateurs (clés étrangères). On ne fait qu'AJOUTER.
         Les 3 formulaires ont la structure commune de la partie 9 de
         style.css (class="form", div.form-group, div.form-actions). --}}

    {{-- ===================== Rôles ===================== --}}
    <h2>Rôles</h2>

    <table class="table">
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
    <form method="POST" action="{{ route('parametrage.roles.store') }}" class="form">
        @csrf
        <div class="form-group">
            <label for="role_code">Code (10 caractères max, ex. SECR) :</label>
            <input type="text" id="role_code" name="role_code" maxlength="10" value="{{ old('role_code') }}">
            @if ($errors->first('role_code'))
                <span class="field-error">{{ $errors->first('role_code') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label for="role_nom">Nom :</label>
            <input type="text" id="role_nom" name="role_nom" maxlength="100" value="{{ old('role_nom') }}">
            @if ($errors->first('role_nom'))
                <span class="field-error">{{ $errors->first('role_nom') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label for="role_commentaire">Commentaire (facultatif) :</label>
            <input type="text" id="role_commentaire" name="role_commentaire" maxlength="250" value="{{ old('role_commentaire') }}">
            @if ($errors->first('role_commentaire'))
                <span class="field-error">{{ $errors->first('role_commentaire') }}</span>
            @endif
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Ajouter le rôle</button>
        </div>
    </form>

    {{-- ===================== Statuts ===================== --}}
    <h2>Statuts</h2>

    <table class="table">
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
    <p class="remarque"><em>Attention : seuls les comptes au statut « A » (Actif) peuvent se connecter.
        Un nouveau statut bloquera donc la connexion des comptes qui l'ont.</em></p>

    <h3>Ajouter un statut</h3>
    <form method="POST" action="{{ route('parametrage.statuts.store') }}" class="form">
        @csrf
        <div class="form-group">
            <label for="statut_code">Code (1 lettre) :</label>
            <input type="text" id="statut_code" name="statut_code" maxlength="1" value="{{ old('statut_code') }}">
            @if ($errors->first('statut_code'))
                <span class="field-error">{{ $errors->first('statut_code') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label for="statut_nom">Nom :</label>
            <input type="text" id="statut_nom" name="statut_nom" maxlength="100" value="{{ old('statut_nom') }}">
            @if ($errors->first('statut_nom'))
                <span class="field-error">{{ $errors->first('statut_nom') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label for="statut_commentaire">Commentaire (facultatif) :</label>
            <input type="text" id="statut_commentaire" name="statut_commentaire" maxlength="250" value="{{ old('statut_commentaire') }}">
            @if ($errors->first('statut_commentaire'))
                <span class="field-error">{{ $errors->first('statut_commentaire') }}</span>
            @endif
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Ajouter le statut</button>
        </div>
    </form>

    {{-- ===================== Genres ===================== --}}
    <h2>Genres</h2>

    <table class="table">
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
    <form method="POST" action="{{ route('parametrage.genres.store') }}" class="form">
        @csrf
        <div class="form-group">
            <label for="genre_code">Code (1 lettre) :</label>
            <input type="text" id="genre_code" name="genre_code" maxlength="1" value="{{ old('genre_code') }}">
            @if ($errors->first('genre_code'))
                <span class="field-error">{{ $errors->first('genre_code') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label for="genre_nom">Nom :</label>
            <input type="text" id="genre_nom" name="genre_nom" maxlength="100" value="{{ old('genre_nom') }}">
            @if ($errors->first('genre_nom'))
                <span class="field-error">{{ $errors->first('genre_nom') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label for="genre_commentaire">Commentaire (facultatif) :</label>
            <input type="text" id="genre_commentaire" name="genre_commentaire" maxlength="250" value="{{ old('genre_commentaire') }}">
            @if ($errors->first('genre_commentaire'))
                <span class="field-error">{{ $errors->first('genre_commentaire') }}</span>
            @endif
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Ajouter le genre</button>
        </div>
    </form>
@endsection
