@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

{{-- Page plus large (1200 px) : le tableau des comptes a 8 colonnes. --}}
@section('largeur', 'contenu-large')

@section('content')
    <h1>Gestion des utilisateurs</h1>

    @if (session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
    @endif

    <p><a class="btn btn-primary" href="{{ route('utilisateurs.create') }}">+ Créer un utilisateur</a></p>

    {{-- Le tableau des comptes est un composant Livewire (recherche,
         filtre "en attente", boutons de statut, changement de rôle sans
         recharger la page). Code : app/Livewire/GestionComptes.php et
         resources/views/livewire/gestion-comptes.blade.php --}}
    <livewire:gestion-comptes />
@endsection
