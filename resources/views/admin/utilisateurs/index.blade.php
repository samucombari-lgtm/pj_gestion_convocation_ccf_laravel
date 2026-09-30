<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
</head>
<body>
    <h1>Gestion des utilisateurs</h1>

    <p><a href="{{ route('menu') }}">&larr; Retour au menu</a></p>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <p><a href="{{ route('utilisateurs.create') }}">+ Créer un utilisateur</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Genre</th>
                <th>Classe</th>
                <th>N° candidat</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($utilisateurs as $utilisateur)
                <tr>
                    <td>{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</td>
                    <td>{{ $utilisateur->compte?->email }}</td>
                    <td>{{ $utilisateur->role->nom }}</td>
                    <td>
                        {{ $utilisateur->statut->nom }}
                        {{-- Repère visuel simple pour les comptes bannis,
                             sans rien changer côté base : juste du texte
                             en rouge sur cette page. --}}
                        @if($utilisateur->code_statut === 'B')
                            <span style="color: red;">⚠</span>
                        @endif
                    </td>
                    <td>{{ $utilisateur->genre->nom }}</td>
                    <td>{{ $utilisateur->classe ?? '—' }}</td>
                    <td>{{ $utilisateur->numero_candidat ?? '—' }}</td>
                    <td><a href="{{ route('utilisateurs.edit', $utilisateur) }}">Modifier</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Aucun utilisateur pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
