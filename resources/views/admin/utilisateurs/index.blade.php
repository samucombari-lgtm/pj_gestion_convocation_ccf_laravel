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

    {{-- Le tableau des comptes est un composant Livewire (recherche,
         filtre "en attente", boutons de statut, changement de rôle sans
         recharger la page). Code : app/Livewire/GestionComptes.php et
         resources/views/livewire/gestion-comptes.blade.php --}}
    <livewire:gestion-comptes />
</body>
</html>
