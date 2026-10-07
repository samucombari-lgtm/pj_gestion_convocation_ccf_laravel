@extends('layouts.app')

@section('title', 'Mon planning de jury')

@section('content')
    <h1>Mon planning de jury</h1>

    {{-- $epreuves regroupe déjà les épreuves de TOUS les panels dont je
         fais partie (voir JuryController::planning) : pas besoin de les
         séparer par panel ici, la vue reste simple. --}}
    @forelse ($epreuves as $epreuve)
        <section class="card">
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
@endsection
