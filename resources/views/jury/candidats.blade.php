@extends('layouts.app')

@section('title', 'Mes candidats à évaluer')

@section('content')
    <h1>Mes candidats à évaluer</h1>

    {{-- Un tableau plutôt que des <section> ici : on affiche surtout des
         données courtes (nom, épreuve, heure) à comparer d'un coup d'oeil,
         un tableau est plus lisible qu'une liste de blocs pour ce cas. --}}
    <table class="table">
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
@endsection
