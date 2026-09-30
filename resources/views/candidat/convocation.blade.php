<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma convocation</title>
</head>
<body>
    <h1>Mes convocations</h1>

    <p><a href="{{ route('menu') }}">&larr; Retour au menu</a></p>

    {{-- @forelse affiche le bloc pour chaque convocation, ET gère tout
         seul le cas où la liste est vide grâce à @empty (sans avoir à
         écrire un @if(count($convocations) > 0) nous-mêmes). --}}
    @forelse ($convocations as $convocation)
        <section style="border: 1px solid #ccc; margin-bottom: 1em; padding: 1em;">
            <h2>{{ $convocation->epreuve->nom }}</h2>

            <p>
                <strong>Date et heure :</strong>
                {{-- $convocation->horaire est un objet Carbon (voir le cast
                     dans Passer.php) : translatedFormat() l'affiche en
                     français grâce à APP_LOCALE=fr dans le .env. --}}
                {{ $convocation->horaire->translatedFormat('l d F Y à H:i') }}
            </p>

            <p>
                <strong>Lieu :</strong>
                {{ $convocation->epreuve->lieu->nom }}
                @if($convocation->epreuve->lieu->adresse)
                    ({{ $convocation->epreuve->lieu->adresse }})
                @endif
            </p>

            <p>
                <strong>Jury :</strong>
                {{ $convocation->jury->nom }} ({{ $convocation->jury->code }})
            </p>
        </section>
    @empty
        <p>Vous n'avez aucune convocation pour le moment.</p>
    @endforelse
</body>
</html>
