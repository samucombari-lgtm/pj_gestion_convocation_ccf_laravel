@extends('layouts.app')

@section('title', 'Épreuves')

@section('content')
    <h1>Épreuves</h1>

    {{-- Message de succès flashé par le Controller après une création
         réussie (->with('success', '...')). session('success') renvoie
         null s'il n'y en a pas, donc @if suffit. --}}
    @if (session('success'))
        <p class="alert alert-success">{{ session('success') }}</p>
    @endif

    <p><a class="btn btn-primary" href="{{ route('epreuves.create') }}">+ Créer une session d'épreuves</a></p>

    {{-- .table-defilement : si la fenêtre est étroite, le tableau défile
         horizontalement au lieu d'élargir toute la page. --}}
    <div class="table-defilement">
    <table class="table">
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
                    {{-- .nowrap : la date et l'heure restent sur une ligne. --}}
                    <td class="nowrap">{{ $epreuve->date_debut->translatedFormat('d/m/Y H:i') }}</td>
                    <td class="nowrap">{{ $epreuve->date_fin->translatedFormat('d/m/Y H:i') }}</td>
                    <td class="nowrap">{{ $epreuve->duree_candidat }} min</td>
                    <td>{{ $epreuve->lieu?->nom ?? '—' }}</td>
                    <td>
                        {{-- Même présentation que la colonne Actions de la
                             gestion des comptes : petits boutons côte à côte. --}}
                        <div class="actions">
                            <a class="btn btn-petit" href="{{ route('epreuves.affecter', $epreuve) }}">Affecter un jury</a>
                            <a class="btn btn-petit" href="{{ route('epreuves.convocations', $epreuve) }}">Voir les convocations</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucune épreuve créée pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
@endsection
