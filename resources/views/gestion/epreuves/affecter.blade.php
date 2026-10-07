@extends('layouts.app')

@section('title')
    Affecter un jury - {{ $epreuve->nom }}
@endsection

@section('content')
    <h1>Affecter un jury</h1>

    <p><a class="btn" href="{{ route('epreuves.index') }}">&larr; Retour à la liste des épreuves</a></p>

    <h2>{{ $epreuve->nom }} ({{ $epreuve->code }})</h2>
    <p>
        Du {{ $epreuve->date_debut->translatedFormat('l d F Y à H:i') }}
        au {{ $epreuve->date_fin->translatedFormat('l d F Y à H:i') }}
    </p>

    {{-- Message de succès, ET avertissement de conflit d'intérêt
         (2 messages différents, flashés séparément par le Controller :
         'success' est toujours présent après une affectation réussie,
         'warning' seulement si un conflit d'intérêt possible a été
         détecté). --}}
    @if (session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
    @endif
    @if (session('warning'))
        <p class="alert alert-warning">{{ session('warning') }}</p>
    @endif

    <h3>Panels déjà affectés à cette épreuve</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Panel</th>
                <th>Membres</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($panelsAffectes as $panel)
                <tr>
                    <td>{{ $panel->nom }} ({{ $panel->code }})</td>
                    <td>
                        {{-- implode() sur les noms des membres, séparés par
                             une virgule, plutôt qu'une sous-liste imbriquée :
                             suffisant pour un simple tableau récapitulatif. --}}
                        {{ $panel->membres->map(fn($m) => "{$m->prenom} {$m->nom}")->implode(', ') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Aucun panel affecté pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Affecter un nouveau panel</h3>

    @if ($panelsDisponibles->isEmpty())
        <p>Tous les panels existants sont déjà affectés à cette épreuve.</p>
    @else
        @include('partials.erreurs')

        {{-- Structure commune à tous les formulaires (partie 9 de
             style.css). --}}
        <form method="POST" action="{{ route('epreuves.affecter.store', $epreuve) }}" class="form">
            @csrf

            <div class="form-group">
                <label for="id_jury">Panel :</label>
                <select id="id_jury" name="id_jury">
                    @foreach ($panelsDisponibles as $panel)
                        <option value="{{ $panel->id }}" @selected((int) old('id_jury') === $panel->id)>
                            {{ $panel->nom }} ({{ $panel->code }})
                        </option>
                    @endforeach
                </select>

                {{-- Les erreurs de chevauchement horaire (règle bloquante)
                     arrivent ici, sur le champ id_jury. --}}
                @if ($errors->first('id_jury'))
                    <span class="field-error">{{ $errors->first('id_jury') }}</span>
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Affecter</button>
            </div>
        </form>
    @endif
@endsection
