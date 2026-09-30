<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes candidats à évaluer</title>
</head>
<body>
    <h1>Mes candidats à évaluer</h1>

    <p><a href="{{ route('menu') }}">&larr; Retour au menu</a></p>

    {{-- Un tableau plutôt que des <section> ici : on affiche surtout des
         données courtes (nom, épreuve, heure) à comparer d'un coup d'oeil,
         un tableau est plus lisible qu'une liste de blocs pour ce cas. --}}
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Horaire</th>
                <th>Candidat</th>
                <th>N° candidat</th>
                <th>Épreuve</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($passages as $passage)
                <tr>
                    <td>{{ $passage->horaire->translatedFormat('l d F Y à H:i') }}</td>
                    <td>{{ $passage->utilisateur->prenom }} {{ $passage->utilisateur->nom }}</td>
                    <td>{{ $passage->utilisateur->numero_candidat }}</td>
                    <td>{{ $passage->epreuve->nom }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Aucun candidat à évaluer pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
