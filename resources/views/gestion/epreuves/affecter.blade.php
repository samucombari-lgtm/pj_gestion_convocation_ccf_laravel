<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Affecter un jury - {{ $epreuve->nom }}</title>
</head>
<body>
    <h1>Affecter un jury</h1>

    <p><a href="{{ route('epreuves.index') }}">&larr; Retour à la liste des épreuves</a></p>

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
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    @if (session('warning'))
        <p style="color: darkorange; font-weight: bold;">{{ session('warning') }}</p>
    @endif

    <h3>Panels déjà affectés à cette épreuve</h3>
    <table border="1" cellpadding="6" cellspacing="0">
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
        <form method="POST" action="{{ route('epreuves.affecter.store', $epreuve) }}">
            @csrf

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
                <br><span style="color: red;">{{ $errors->first('id_jury') }}</span>
            @endif

            <button type="submit">Affecter</button>
        </form>
    @endif
</body>
</html>
