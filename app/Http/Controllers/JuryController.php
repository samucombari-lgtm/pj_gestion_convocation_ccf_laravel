<?php

namespace App\Http\Controllers;

use App\Models\Passer;

class JuryController extends Controller
{
    // HYPOTHÈSE À CONFIRMER AVEC LE PROFESSEUR : on suppose ici que
    // mcd_former représente la composition d'un panel de jury (qui siège
    // dans quel panel), pas une relation pédagogique "a formé cet élève".
    // Si ce n'est pas le cas, les deux méthodes ci-dessous seront à revoir.

    // "Mon planning de jury" : les épreuves couvertes par mon ou mes panels.
    public function planning()
    {
        if (! auth()->user()->isJury()) {
            abort(403);
        }

        // Tous les panels dont je fais partie (table pivot mcd_former),
        // avec pour chacun les épreuves affectées (table pivot
        // mcd_affecter) et le lieu de chaque épreuve. Les 3 relations
        // sont chargées en quelques requêtes groupées grâce à with(),
        // plutôt qu'une requête par panel puis une par épreuve (N+1).
        $jurys = auth()->user()->utilisateur
            ->jurys()
            ->with('epreuves.lieu')
            ->get();

        // $jurys est une Collection de panels, chacun avec sa propre
        // liste d'épreuves. On aplatit tout ça en une seule liste
        // d'épreuves : pluck('epreuves') récupère la liste des épreuves
        // de chaque panel, collapse() fusionne ces listes en une seule,
        // unique('id') retire les doublons (si jamais 2 de mes panels
        // étaient affectés à la même épreuve), et sortBy trie par date.
        $epreuves = $jurys->pluck('epreuves')
            ->collapse()
            ->unique('id')
            ->sortBy('date_debut')
            ->values();

        return view('jury.planning', [
            'epreuves' => $epreuves,
        ]);
    }

    // "Mes candidats à évaluer" : les passages (mcd_passer) dont le jury
    // (id_jury) est un des panels dont je fais partie.
    public function candidats()
    {
        if (! auth()->user()->isJury()) {
            abort(403);
        }

        // On ne récupère ici que les identifiants des panels dont je fais
        // partie (pas les épreuves cette fois, pas besoin).
        $idsDeMesPanels = auth()->user()->utilisateur
            ->jurys()
            ->pluck('mcd_jurys.id');

        // Toutes les lignes mcd_passer dont id_jury est un de ces panels,
        // avec le candidat et l'épreuve préchargés (encore une fois pour
        // éviter le N+1 dans la vue), triées par horaire.
        $passages = Passer::whereIn('id_jury', $idsDeMesPanels)
            ->with(['utilisateur', 'epreuve'])
            ->orderBy('horaire')
            ->get();

        return view('jury.candidats', [
            'passages' => $passages,
        ]);
    }
}
