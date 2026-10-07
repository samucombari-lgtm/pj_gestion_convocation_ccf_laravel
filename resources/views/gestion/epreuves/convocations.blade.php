@extends('layouts.app')

@section('title')
    Convocations - {{ $epreuve->nom }}
@endsection

@section('content')
    <h1>Convocations : {{ $epreuve->nom }} ({{ $epreuve->code }})</h1>

    {{-- window.print() ouvre juste la boîte de dialogue d'impression du
         navigateur : c'est tout ce qu'on demande ("prête à être imprimée"),
         pas besoin de vrai envoi d'email pour l'instant. Seule ligne de
         JavaScript de toute l'appli, et volontairement minimale.
         À l'impression, la barre de menu et les boutons sont cachés
         (partie "Impression" de style.css). --}}
    <p>
        <a href="{{ route('epreuves.index') }}">&larr; Retour à la liste des épreuves</a>
        &nbsp;|&nbsp;
        <button type="button" class="btn" onclick="window.print()">Imprimer cette page</button>
    </p>

    <p>
        Du {{ $epreuve->date_debut->translatedFormat('l d F Y à H:i') }}
        au {{ $epreuve->date_fin->translatedFormat('l d F Y à H:i') }}
        — Lieu : {{ $epreuve->lieu?->nom ?? '—' }}
        @if($epreuve->lieu?->adresse)
            ({{ $epreuve->lieu->adresse }})
        @endif
    </p>

    {{-- IMPORTANT : cette page ne montre QUE le contenu des convocations
         (qui, quand, où, avec quel jury). Il n'existe aucune colonne en
         base pour savoir si une convocation a réellement été envoyée,
         vue ou confirmée (mcd_passer n'a pas de date_envoi/statut), et on
         ne modifie pas la structure de la base sans l'accord du
         professeur. Impossible donc d'afficher un état "envoyé" fiable :
         on ne l'invente pas, et il n'y a pas de bouton "Marquer comme
         envoyé" - un tel bouton ne pourrait rien enregistrer de
         permanent et donnerait une fausse impression de suivi. --}}
    <p class="remarque"><em>Cette page affiche le contenu des convocations à titre de consultation/impression. Elle ne trace pas d'état "envoyé" (aucune colonne prévue à cet effet en base).</em></p>

    <h2>Convocations candidats</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Candidat</th>
                <th>N° candidat</th>
                <th>Horaire</th>
                <th>Lieu</th>
                <th>Jury</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($passages as $passage)
                <tr>
                    <td>{{ $passage->utilisateur->prenom }} {{ $passage->utilisateur->nom }}</td>
                    <td>{{ $passage->utilisateur->numero_candidat }}</td>
                    <td>{{ $passage->horaire->translatedFormat('l d F Y à H:i') }}</td>
                    <td>{{ $epreuve->lieu?->nom ?? '—' }}</td>
                    <td>{{ $passage->jury->nom }} ({{ $passage->jury->code }})</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Aucun candidat affecté à cette épreuve pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Convocations jurys</h2>
    @forelse ($panels as $panel)
        <section class="card">
            <h3>{{ $panel->nom }} ({{ $panel->code }})</h3>

            <p>
                <strong>Membres du panel :</strong>
                {{ $panel->membres->map(fn($m) => "{$m->prenom} {$m->nom}")->implode(', ') }}
            </p>

            {{-- $passages->where('id_jury', $panel->id) : on filtre en
                 mémoire la liste déjà chargée plus haut, plutôt que de
                 refaire une requête SQL par panel. --}}
            <p><strong>Candidats à évaluer :</strong></p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Candidat</th>
                        <th>Horaire</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($passages->where('id_jury', $panel->id) as $passage)
                        <tr>
                            <td>{{ $passage->utilisateur->prenom }} {{ $passage->utilisateur->nom }}</td>
                            <td>{{ $passage->horaire->translatedFormat('l d F Y à H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">Aucun candidat pour ce panel pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    @empty
        <p>Aucun panel de jury affecté à cette épreuve pour le moment.</p>
    @endforelse
@endsection
