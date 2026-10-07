@extends('layouts.app')

@section('title', 'Créer une session d\'épreuves')

@section('content')
    <h1>Créer une session d'épreuves</h1>

    <p><a class="btn" href="{{ route('epreuves.index') }}">&larr; Retour à la liste des épreuves</a></p>

    @if (session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
    @endif

    @include('partials.erreurs')

    {{-- ===================== Formulaire épreuve ===================== --}}
    {{-- Structure commune à tous les formulaires (voir la partie 9 de
         style.css) : class="form", un div.form-group par champ, et les
         boutons dans div.form-actions. --}}
    <form method="POST" action="{{ route('epreuves.store') }}" class="form">
        @csrf

        <div class="form-group">
            <label for="code">Code (ex. E5S1) :</label>
            <input type="text" id="code" name="code" value="{{ old('code') }}">
            {{-- $errors->first('code') renvoie le 1er message de validation
                 pour ce champ, ou une chaîne vide s'il n'y en a pas. --}}
            @if ($errors->first('code'))
                <span class="field-error">{{ $errors->first('code') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="nom">Nom de l'épreuve :</label>
            <input type="text" id="nom" name="nom" value="{{ old('nom') }}">
            @if ($errors->first('nom'))
                <span class="field-error">{{ $errors->first('nom') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="date_debut">Date et heure de début :</label>
            {{-- type="datetime-local" affiche un vrai sélecteur de date +
                 heure dans le navigateur, sans JavaScript à écrire. --}}
            <input type="datetime-local" id="date_debut" name="date_debut" value="{{ old('date_debut') }}">
            @if ($errors->first('date_debut'))
                <span class="field-error">{{ $errors->first('date_debut') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="date_fin">Date et heure de fin :</label>
            <input type="datetime-local" id="date_fin" name="date_fin" value="{{ old('date_fin') }}">
            @if ($errors->first('date_fin'))
                <span class="field-error">{{ $errors->first('date_fin') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="duree_candidat">Durée de passage par candidat (en minutes) :</label>
            <input type="number" id="duree_candidat" name="duree_candidat" min="1" value="{{ old('duree_candidat') }}">
            @if ($errors->first('duree_candidat'))
                <span class="field-error">{{ $errors->first('duree_candidat') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="id_lieu">Lieu :</label>
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
                <span class="field-error">{{ $errors->first('id_lieu') }}</span>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer l'épreuve</button>
        </div>
    </form>

    {{-- ===================== Formulaire nouveau lieu ===================== --}}
    {{-- <details>/<summary> est un élément HTML natif qui s'ouvre/se ferme
         au clic, sans une seule ligne de JavaScript : exactement ce qu'il
         faut pour "un lien qui ouvre un petit formulaire séparé". On le
         laisse ouvert automatiquement (attribut "open") si une erreur de
         validation concerne un champ "lieu_*", pour que l'utilisateur
         voie tout de suite pourquoi ça a échoué.
         La classe "form" est aussi sur le <details> : le bloc a la même
         largeur et le même centrage que le formulaire de l'épreuve. --}}
    <details class="card form" @if ($errors->first('lieu_code') || $errors->first('lieu_nom')) open @endif>
        <summary>+ Ajouter un nouveau lieu</summary>

        <form method="POST" action="{{ route('lieux.store') }}" class="form">
            @csrf

            <div class="form-group">
                <label for="lieu_code">Code du lieu (ex. SALLE104) :</label>
                <input type="text" id="lieu_code" name="lieu_code" value="{{ old('lieu_code') }}">
                @if ($errors->first('lieu_code'))
                    <span class="field-error">{{ $errors->first('lieu_code') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="lieu_nom">Nom du lieu :</label>
                <input type="text" id="lieu_nom" name="lieu_nom" value="{{ old('lieu_nom') }}">
                @if ($errors->first('lieu_nom'))
                    <span class="field-error">{{ $errors->first('lieu_nom') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="lieu_adresse">Adresse (facultatif) :</label>
                <input type="text" id="lieu_adresse" name="lieu_adresse" value="{{ old('lieu_adresse') }}">
                @if ($errors->first('lieu_adresse'))
                    <span class="field-error">{{ $errors->first('lieu_adresse') }}</span>
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Créer ce lieu</button>
            </div>
        </form>
    </details>
@endsection
