<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Épreuves</title>
</head>
<body>
    <h1>Épreuves</h1>

    <p><a href="{{ route('menu') }}">&larr; Retour au menu</a></p>

    {{-- Message de succès flashé par le Controller après une création
         réussie (->with('success', '...')). session('success') renvoie
         null s'il n'y en a pas, donc @if suffit. --}}
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <p><a href="{{ route('epreuves.create') }}">+ Créer une session d'épreuves</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Durée / candidat</th>
                <th>Lieu</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($epreuves as $epreuve)
                <tr>
                    <td>{{ $epreuve->code }}</td>
                    <td>{{ $epreuve->nom }}</td>
                    <td>{{ $epreuve->date_debut->translatedFormat('d/m/Y H:i') }}</td>
                    <td>{{ $epreuve->date_fin->translatedFormat('d/m/Y H:i') }}</td>
                    <td>{{ $epreuve->duree_candidat }} min</td>
                    <td>{{ $epreuve->lieu?->nom ?? '—' }}</td>
                    <td>
                        <a href="{{ route('epreuves.affecter', $epreuve) }}">Affecter un jury</a>
                        &nbsp;|&nbsp;
                        <a href="{{ route('epreuves.convocations', $epreuve) }}">Voir les convocations</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucune épreuve créée pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
