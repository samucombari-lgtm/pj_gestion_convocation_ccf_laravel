<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
</head>
<body>
    {{-- auth()->user() renvoie l'utilisateur connecté (le Model User),
         rempli automatiquement par Laravel grâce à la session créée
         lors de Auth::attempt() dans LoginController. --}}
    <h1>Bonjour {{ auth()->user()->name }}</h1>

    <nav>
        {{-- Chaque bloc @if n'affiche ses liens QUE si hasRole() renvoie
             true pour ce code de rôle. hasRole() est la méthode qu'on a
             écrite dans User.php tout à l'heure. --}}

        @if(auth()->user()->hasRole('ADMIN'))
            <p><a href="{{ route('utilisateurs.index') }}">Gestion des utilisateurs</a></p>
            <p><a href="#">Paramétrage (rôles, statuts, genres)</a></p>
        @endif

        @if(auth()->user()->hasRole('GEST'))
            <p><a href="{{ route('epreuves.index') }}">Créer une session d'épreuves</a></p>
            {{-- On réutilise la même liste d'épreuves : chaque ligne a un
                 lien "Affecter un jury" (voir gestion/epreuves/index.blade.php),
                 pas besoin d'une 2e liste séparée juste pour ça. --}}
            <p><a href="{{ route('epreuves.index') }}">Affecter les jurys</a></p>
            {{-- Même chose : "Voir les convocations" est un lien par
                 ligne dans la liste des épreuves. --}}
            <p><a href="{{ route('epreuves.index') }}">Envoyer les convocations</a></p>
        @endif

        @if(auth()->user()->hasRole('CAND'))
            <p><a href="{{ route('convocation') }}">Ma convocation</a></p>
        @endif

        @if(auth()->user()->hasRole('JURY'))
            <p><a href="{{ route('jury.planning') }}">Mon planning de jury</a></p>
            <p><a href="{{ route('jury.candidats') }}">Mes candidats à évaluer</a></p>
        @endif
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Déconnexion</button>
    </form>
</body>
</html>