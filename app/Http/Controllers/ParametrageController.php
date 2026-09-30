<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Role;
use App\Models\Statut;
use Illuminate\Http\Request;

class ParametrageController extends Controller
{
    // Page "Paramétrage" : affiche les 3 tables de référence (rôles,
    // statuts, genres) et permet d'y AJOUTER une valeur.
    //
    // Pas de suppression (ni de modification) pour l'instant : ces tables
    // sont référencées par des clés étrangères dans mcd_utilisateurs.
    // Supprimer une valeur encore utilisée casserait des données existantes.
    //
    // Pourquoi 3 méthodes storeRole()/storeStatut()/storeGenre() plutôt
    // qu'une seule méthode générique ? Parce que les 3 tables ne se
    // ressemblent pas tout à fait :
    //   - mcd_roles   : clé primaire "id" auto-incrémentée, "code" en VARCHAR(10) ;
    //   - mcd_statuts : clé primaire "code" en CHAR(1), choisie à la main ;
    //   - mcd_genres  : pareil que mcd_statuts.
    // Une méthode générique devrait recevoir le nom de la table dans l'URL,
    // puis choisir le modèle et les règles de validation selon ce nom
    // (avec des if/match) : plus court de quelques lignes, mais plus dur à
    // lire. Trois petites méthodes explicites restent plus simples.

    private function ensureAdmin(): void
    {
        if (! auth()->user()->hasRole('ADMIN')) {
            abort(403);
        }
    }

    // Affiche les 3 listes + les 3 formulaires d'ajout.
    public function index()
    {
        $this->ensureAdmin();

        return view('admin.parametrage', [
            'roles' => Role::orderBy('nom')->get(),
            'statuts' => Statut::orderBy('nom')->get(),
            'genres' => Genre::orderBy('nom')->get(),
        ]);
    }

    // Comme pour LieuController, les champs sont préfixés (role_code,
    // statut_code, genre_code...) : la page contient TROIS formulaires, et
    // avec les mêmes noms de champs Laravel mélangerait les erreurs de
    // validation et les old() entre eux.
    //
    // Le code est mis en MAJUSCULES avant l'enregistrement, pour rester
    // cohérent avec les valeurs existantes (ADMIN, GEST, 'A', 'B'...).
    // La règle "unique" ne laisse pas passer "gest" si "GEST" existe déjà :
    // la base compare le texte sans tenir compte de la casse
    // (collation utf8mb4_unicode_ci).

    public function storeRole(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            // Le code est obligatoire ici même s'il peut être NULL en base :
            // c'est lui que hasRole('ADMIN') compare, un rôle sans code
            // serait inutilisable dans l'application.
            // alpha_dash = lettres, chiffres, "-" et "_" seulement.
            'role_code' => ['required', 'alpha_dash', 'max:10', 'unique:mcd_roles,code'],
            'role_nom' => ['required', 'string', 'max:100', 'unique:mcd_roles,nom'],
            'role_commentaire' => ['nullable', 'string', 'max:250'],
        ], [
            'role_code.unique' => 'Ce code de rôle existe déjà.',
            'role_nom.unique' => 'Ce nom de rôle existe déjà.',
        ]);

        $role = Role::create([
            'code' => strtoupper($data['role_code']),
            'nom' => $data['role_nom'],
            'commentaire' => $data['role_commentaire'] ?? null,
        ]);

        return redirect()->route('parametrage.index')
            ->with('success', "Rôle « {$role->nom} » ajouté.");
    }

    public function storeStatut(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            // size:1 car la colonne est un CHAR(1) ; alpha = une lettre.
            'statut_code' => ['required', 'alpha', 'size:1', 'unique:mcd_statuts,code'],
            'statut_nom' => ['required', 'string', 'max:100', 'unique:mcd_statuts,nom'],
            'statut_commentaire' => ['nullable', 'string', 'max:250'],
        ], [
            'statut_code.unique' => 'Ce code de statut existe déjà.',
            'statut_code.size' => 'Le code doit faire exactement 1 lettre.',
            'statut_nom.unique' => 'Ce nom de statut existe déjà.',
        ]);

        $statut = Statut::create([
            'code' => strtoupper($data['statut_code']),
            'nom' => $data['statut_nom'],
            'commentaire' => $data['statut_commentaire'] ?? null,
        ]);

        return redirect()->route('parametrage.index')
            ->with('success', "Statut « {$statut->nom} » ajouté.");
    }

    public function storeGenre(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'genre_code' => ['required', 'alpha', 'size:1', 'unique:mcd_genres,code'],
            'genre_nom' => ['required', 'string', 'max:100', 'unique:mcd_genres,nom'],
            'genre_commentaire' => ['nullable', 'string', 'max:250'],
        ], [
            'genre_code.unique' => 'Ce code de genre existe déjà.',
            'genre_code.size' => 'Le code doit faire exactement 1 lettre.',
            'genre_nom.unique' => 'Ce nom de genre existe déjà.',
        ]);

        $genre = Genre::create([
            'code' => strtoupper($data['genre_code']),
            'nom' => $data['genre_nom'],
            'commentaire' => $data['genre_commentaire'] ?? null,
        ]);

        return redirect()->route('parametrage.index')
            ->with('success', "Genre « {$genre->nom} » ajouté.");
    }
}
