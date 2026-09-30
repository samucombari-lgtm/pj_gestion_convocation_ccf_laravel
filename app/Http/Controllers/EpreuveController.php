<?php

namespace App\Http\Controllers;

use App\Models\Epreuve;
use App\Models\Lieu;
use Illuminate\Http\Request;

class EpreuveController extends Controller
{
    // Petite méthode privée pour ne pas répéter le même contrôle
    // hasRole('GEST') dans chacune des 3 méthodes ci-dessous.
    private function ensureGestionnaire(): void
    {
        if (! auth()->user()->hasRole('GEST')) {
            abort(403);
        }
    }

    // Liste des épreuves déjà créées.
    public function index()
    {
        $this->ensureGestionnaire();

        // with('lieu') précharge le lieu de chaque épreuve en une seule
        // requête groupée, pour éviter le problème du N+1 dans la vue
        // (comme pour les pages candidat/jury vues précédemment).
        $epreuves = Epreuve::with('lieu')->orderBy('date_debut')->get();

        return view('gestion.epreuves.index', ['epreuves' => $epreuves]);
    }

    // Affiche le formulaire de création d'une nouvelle épreuve.
    public function create()
    {
        $this->ensureGestionnaire();

        // La liste déroulante des lieux existants, pour le <select> du
        // formulaire. Si le gestionnaire ajoute un nouveau lieu (via le
        // petit formulaire séparé, cf. LieuController), il sera de
        // nouveau proposé ici au rechargement de la page.
        $lieux = Lieu::orderBy('nom')->get();

        return view('gestion.epreuves.create', ['lieux' => $lieux]);
    }

    // Enregistre la nouvelle épreuve.
    public function store(Request $request)
    {
        $this->ensureGestionnaire();

        // $request->validate() vérifie chaque champ et renvoie
        // automatiquement en arrière (avec les erreurs et les valeurs
        // déjà saisies) si quelque chose ne va pas - on n'a jamais à
        // écrire nous-mêmes le cas "erreur". Les champs de CE formulaire
        // s'appellent "code"/"nom" (le formulaire "nouveau lieu" sur la
        // même page utilise "lieu_code"/"lieu_nom" pour ne jamais se
        // mélanger avec ceux-ci, voir LieuController::store()).
        //
        // 'unique:mcd_epreuves,code' : la table a déjà une contrainte
        // UNIQUE en base (vue dans create_v0.sql.txt), mais SANS cette
        // règle de validation, un code déjà pris ferait planter la
        // requête SQL avec une erreur brute (1062 Duplicate entry) au
        // lieu d'un message clair sous le champ du formulaire.
        $data = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:mcd_epreuves,code'],
            'nom' => ['required', 'string', 'max:100', 'unique:mcd_epreuves,nom'],
            'date_debut' => ['required', 'date'],
            // 'after:date_debut' : la date de fin doit être strictement
            // après la date de début.
            'date_fin' => ['required', 'date', 'after:date_debut'],
            // 'integer' + 'min:1' : un entier strictement positif.
            'duree_candidat' => ['required', 'integer', 'min:1'],
            'id_lieu' => ['required', 'exists:mcd_lieux,id'],
        ], [
            'code.unique' => "Ce code d'épreuve existe déjà, choisis-en un autre.",
            'nom.unique' => 'Ce nom d\'épreuve existe déjà, choisis-en un autre.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
            'id_lieu.exists' => 'Le lieu sélectionné est invalide.',
        ]);

        Epreuve::create($data);

        return redirect()->route('epreuves.index')->with('success', 'Épreuve créée avec succès.');
    }

    // Contenu des convocations (candidats + jurys) pour UNE épreuve, à
    // consulter/imprimer par le gestionnaire. Le nom de la méthode reste
    // "convocations" (pas un nouveau contrôleur "ConvocationGestion...")
    // car c'est une simple page de LECTURE scopée à une épreuve, comme
    // index()/create() ci-dessus - contrairement à AffectationController,
    // qui a sa propre logique de vérification et modifie mcd_affecter.
    // Le nom de la ROUTE ('epreuves.convocations', au pluriel) évite toute
    // confusion avec la route candidat 'convocation' (singulier) de
    // ConvocationController, qui montre SES PROPRES convocations à travers
    // plusieurs épreuves - c'est l'exact inverse de cette page-ci, qui
    // montre TOUS les candidats et TOUS les jurys d'UNE SEULE épreuve.
    public function convocations(Epreuve $epreuve)
    {
        $this->ensureGestionnaire();

        // On précharge son lieu (une seule requête en plus, pas grave ici
        // puisqu'il n'y a qu'une épreuve sur cette page, donc pas de N+1).
        $epreuve->load('lieu');

        // Liste 1 - "convocation candidat" : chaque candidat affecté à
        // cette épreuve (mcd_passer), avec son horaire précis et le panel
        // de jury qui l'évalue.
        $passages = $epreuve->passages()
            ->with(['utilisateur', 'jury'])
            ->orderBy('horaire')
            ->get();

        // Liste 2 - "convocation jury" : chaque panel affecté à cette
        // épreuve (mcd_affecter), avec ses membres (mcd_former). La vue
        // affichera, pour chaque panel, les candidats qu'IL évalue en
        // filtrant $passages par id_jury - pas besoin d'une requête
        // supplémentaire par panel.
        $panels = $epreuve->jurys()->with('membres')->get();

        return view('gestion.epreuves.convocations', [
            'epreuve' => $epreuve,
            'passages' => $passages,
            'panels' => $panels,
        ]);
    }
}
