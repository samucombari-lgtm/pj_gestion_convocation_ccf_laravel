<?php

namespace App\Http\Controllers;

use App\Models\Epreuve;
use App\Models\Jury;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    // Choix : un contrôleur dédié (comme LieuController), plutôt qu'une
    // méthode de plus dans EpreuveController. EpreuveController gère les
    // épreuves elles-mêmes (créer, lister) ; ici on gère une relation
    // différente (mcd_affecter, quel panel couvre quelle épreuve), avec
    // sa propre logique de vérification (chevauchement + conflit
    // d'intérêt) assez conséquente pour mériter son propre fichier.

    private function ensureGestionnaire(): void
    {
        if (! auth()->user()->hasRole('GEST')) {
            abort(403);
        }
    }

    // GET /gestion/epreuves/{epreuve}/affecter
    // $epreuve est injecté automatiquement par Laravel à partir de l'id
    // dans l'URL (route model binding) : pas besoin d'écrire
    // Epreuve::findOrFail($id) nous-mêmes.
    public function create(Epreuve $epreuve)
    {
        $this->ensureGestionnaire();

        // Panels déjà affectés à cette épreuve, avec leurs membres
        // préchargés (utile pour la vue, qui peut afficher qui est dans
        // chaque panel déjà en place).
        $panelsAffectes = $epreuve->jurys()->with('membres')->get();

        // Panels PAS encore affectés à cette épreuve : on prend tous les
        // ids déjà affectés, et on exclut ces ids de la liste complète
        // des panels pour construire le <select> du formulaire.
        $idsDejaAffectes = $panelsAffectes->pluck('id');
        $panelsDisponibles = Jury::whereNotIn('id', $idsDejaAffectes)->orderBy('code')->get();

        return view('gestion.epreuves.affecter', [
            'epreuve' => $epreuve,
            'panelsAffectes' => $panelsAffectes,
            'panelsDisponibles' => $panelsDisponibles,
        ]);
    }

    // POST /gestion/epreuves/{epreuve}/affecter
    public function store(Request $request, Epreuve $epreuve)
    {
        $this->ensureGestionnaire();

        $data = $request->validate([
            'id_jury' => ['required', 'exists:mcd_jurys,id'],
        ], [
            'id_jury.required' => 'Choisis un panel à affecter.',
        ]);

        $jury = Jury::with('epreuves')->findOrFail($data['id_jury']);

        // Garde-fou : normalement le <select> ne propose déjà que des
        // panels pas encore affectés (voir create() ci-dessus), mais si
        // la page était restée ouverte longtemps et qu'un autre
        // gestionnaire a affecté ce panel entre-temps, on évite de
        // planter sur l'erreur SQL de clé primaire en double
        // (mcd_affecter a pour clé primaire id_epreuve + id_jury).
        if ($jury->epreuves->contains('id', $epreuve->id)) {
            return back()->withErrors([
                'id_jury' => "Le panel {$jury->nom} est déjà affecté à cette épreuve.",
            ]);
        }

        // ===================================================================
        // RÈGLE 1 (sûre, on BLOQUE) : un même panel ne peut pas se trouver à
        // 2 endroits en même temps. On regarde toutes les épreuves déjà
        // couvertes par ce panel, et on vérifie si l'une d'elles chevauche
        // dans le temps l'épreuve qu'on veut affecter.
        //
        // Deux plages [debut1, fin1] et [debut2, fin2] se chevauchent si :
        //     debut1 < fin2  ET  debut2 < fin1
        // (c'est le test classique de chevauchement d'intervalles - si
        // l'une des deux plages se termine avant que l'autre commence, il
        // n'y a pas de chevauchement, et c'est le seul cas où ce n'est
        // pas vrai).
        // ===================================================================
        foreach ($jury->epreuves as $autreEpreuve) {
            $chevauchement = $epreuve->date_debut->lt($autreEpreuve->date_fin)
                && $autreEpreuve->date_debut->lt($epreuve->date_fin);

            if ($chevauchement) {
                return back()->withErrors([
                    'id_jury' => "Impossible d'affecter {$jury->nom} : ce panel est déjà affecté à "
                        . "« {$autreEpreuve->nom} » du {$autreEpreuve->date_debut->translatedFormat('d/m/Y H:i')} "
                        . "au {$autreEpreuve->date_fin->translatedFormat('d/m/Y H:i')}, "
                        . 'qui chevauche les horaires de cette épreuve.',
                ]);
            }
        }

        // On crée la ligne mcd_affecter. attach() ajoute cette seule
        // association sans toucher aux autres affectations existantes
        // (contrairement à sync(), qui remplacerait toute la liste).
        $epreuve->jurys()->attach($jury->id);

        // ===================================================================
        // RÈGLE 2 (hypothèse NON confirmée par le professeur, on AVERTIT
        // seulement) : un membre de ce panel (mcd_former) est-il aussi
        // candidat sur CETTE épreuve précise (mcd_passer) ? Si oui, il y a
        // potentiellement un conflit d'intérêt à vérifier à la main, mais
        // on ne bloque jamais l'affectation pour ce cas.
        // ===================================================================
        $membresDuPanel = $jury->membres; // Collection d'Utilisateur
        $idsCandidatsDeLEpreuve = $epreuve->passages()->pluck('id_utilisateur');
        $conflits = $membresDuPanel->whereIn('id', $idsCandidatsDeLEpreuve);

        if ($conflits->isNotEmpty()) {
            $noms = $conflits->map(fn ($u) => "{$u->prenom} {$u->nom}")->implode(', ');

            return redirect()->route('epreuves.affecter', $epreuve)
                ->with('success', "Panel {$jury->nom} affecté à cette épreuve.")
                ->with('warning', "⚠ Conflit d'intérêt possible : {$noms} fait partie de ce panel ET passe "
                    . 'cette épreuve - à vérifier manuellement (règle non confirmée par le professeur référent).');
        }

        return redirect()->route('epreuves.affecter', $epreuve)
            ->with('success', "Panel {$jury->nom} affecté à cette épreuve.");
    }
}
