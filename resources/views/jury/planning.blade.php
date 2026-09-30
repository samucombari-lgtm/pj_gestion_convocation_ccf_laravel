<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon planning de jury</title>
</head>
<body>
    <h1>Mon planning de jury</h1>

    <p><a href="{{ route('menu') }}">&larr; Retour au menu</a></p>

    {{-- $epreuves regroupe déjà les épreuves de TOUS les panels dont je
         fais partie (voir JuryController::planning) : pas besoin de les
         séparer par panel ici, la vue reste simple. --}}
    @forelse ($epreuves as $epreuve)
        <section style="border: 1px solid #ccc; margin-bottom: 1em; padding: 1em;">
            <h2>{{ $epreuve->nom }}</h2>

            <p>
                <strong>Début :</strong>
                {{ $epreuve->date_debut->translatedFormat('l d F Y à H:i') }}
            </p>
            <p>
                <strong>Fin :</strong>
                {{ $epreuve->date_fin->translatedFormat('l d F Y à H:i') }}
            </p>

            <p>
                <strong>Lieu :</strong>
                {{ $epreuve->lieu->nom }}
                @if($epreuve->lieu->adresse)
                    ({{ $epreuve->lieu->adresse }})
                @endif
            </p>
        </section>
    @empty
        <p>Aucune épreuve ne vous est affectée pour le moment.</p>
    @endforelse
</body>
</html>
