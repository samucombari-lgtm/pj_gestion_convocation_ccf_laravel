<?php

namespace App\Http\Controllers;

use App\Models\Lieu;
use Illuminate\Http\Request;

class LieuController extends Controller
{
    // Pas de index()/create()/edit() ici : pour l'instant, un lieu ne se
    // crée que depuis le petit formulaire intégré à la page "Créer une
    // session d'épreuves" (voir gestion/epreuves/create.blade.php). On
    // ajoutera une gestion complète des lieux plus tard si besoin.
    public function store(Request $request)
    {
        if (! auth()->user()->hasRole('GEST')) {
            abort(403);
        }

        // Les champs de ce formulaire s'appellent "lieu_code", "lieu_nom",
        // "lieu_adresse" (et pas juste "code", "nom") : la page de
        // création d'épreuve contient DEUX formulaires (épreuve + lieu).
        // Avec des noms de champs identiques, Laravel mélangerait les
        // erreurs de validation ET les anciennes valeurs saisies (old())
        // entre les deux formulaires en cas d'erreur. Des noms différents
        // évitent le problème simplement, sans mécanisme compliqué.
        $data = $request->validate([
            'lieu_code' => ['required', 'string', 'max:10', 'unique:mcd_lieux,code'],
            'lieu_nom' => ['required', 'string', 'max:100', 'unique:mcd_lieux,nom'],
            'lieu_adresse' => ['nullable', 'string', 'max:200'],
        ], [
            'lieu_code.unique' => 'Ce code de lieu existe déjà.',
            'lieu_nom.unique' => 'Ce nom de lieu existe déjà.',
        ]);

        $lieu = Lieu::create([
            'code' => $data['lieu_code'],
            'nom' => $data['lieu_nom'],
            'adresse' => $data['lieu_adresse'] ?? null,
        ]);

        // On retourne directement vers le formulaire de création
        // d'épreuve : c'est le seul endroit où ce formulaire est utilisé
        // pour l'instant, et le nouveau lieu y apparaîtra aussitôt dans
        // la liste déroulante.
        return redirect()->route('epreuves.create')
            ->with('success', "Lieu « {$lieu->nom} » créé. Tu peux maintenant le sélectionner ci-dessous.");
    }
}
