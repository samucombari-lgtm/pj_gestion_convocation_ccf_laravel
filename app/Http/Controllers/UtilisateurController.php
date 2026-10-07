<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Role;
use App\Models\Statut;
use App\Models\User;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    // Le contrôleur s'appelle UtilisateurController (au pluriel implicite
    // pour ses routes) alors que le modèle s'appelle Utilisateur (singulier).
    // On ne se sert PAS de Route::resource() ici, qui aurait pu générer
    // automatiquement des noms de route à partir du nom donné en argument -
    // toutes les routes de cette appli sont déjà déclarées à la main avec
    // un ->name(...) explicite (voir routes/web.php), donc pas d'ambiguïté
    // possible sur le nom réellement utilisé : on garde ce style partout.
    private function ensureAdmin(): void
    {
        if (! auth()->user()->hasRole('ADMIN')) {
            abort(403);
        }
    }

    // Page "Gestion des utilisateurs". La liste elle-même (recherche,
    // filtre, boutons de statut, changement de rôle) est gérée par le
    // composant Livewire App\Livewire\GestionComptes, inclus dans la vue :
    // le contrôleur se contente donc de vérifier le rôle et d'afficher
    // la page.
    public function index()
    {
        $this->ensureAdmin();

        return view('admin.utilisateurs.index');
    }

    // Formulaire de création.
    public function create()
    {
        $this->ensureAdmin();

        return view('admin.utilisateurs.create', [
            'roles' => Role::orderBy('nom')->get(),
            'statuts' => Statut::orderBy('nom')->get(),
            'genres' => Genre::orderBy('nom')->get(),
        ]);
    }

    // Enregistre un nouvel utilisateur.
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:50'],
            'prenom' => ['required', 'string', 'max:50'],
            'classe' => ['nullable', 'string', 'max:10'],
            'numero_candidat' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:mcd_users,email'],
            'password' => ['required', 'string', 'min:8'],
            'id_role' => ['required', 'exists:mcd_roles,id'],
            'code_statut' => ['required', 'exists:mcd_statuts,code'],
            'code_genre' => ['required', 'exists:mcd_genres,code'],
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // =====================================================================
        // POINT TECHNIQUE IMPORTANT : mcd_utilisateurs.id n'a PAS son propre
        // auto-incrément. Regarde create_v0.sql.txt : la table est créée avec
        //     id BIGINT UNSIGNED,
        //     ...
        //     FOREIGN KEY(id) REFERENCES mcd_users(id)
        // Son id est une clé PARTAGÉE : elle réutilise TOUJOURS l'id de la
        // ligne mcd_users correspondante. Il ne faut donc JAMAIS générer cet
        // id nous-mêmes (ni via du SQL brut, ni en devinant le prochain id
        // disponible) - c'est fragile et ça peut créer un id qui existe déjà
        // ou qui ne correspond à aucun compte.
        //
        // La bonne méthode, en 2 temps :
        //
        // 1) Créer D'ABORD le compte de connexion (mcd_users), qui a lui un
        //    vrai auto-incrément (SERIAL). $user->id est généré par MySQL à
        //    la sauvegarde. Le cast 'password' => 'hashed' (déjà défini dans
        //    User.php) hache automatiquement le mot de passe en clair qu'on
        //    lui donne ici - inutile d'appeler Hash::make() nous-mêmes.
        // 2) Créer ENSUITE la fiche métier (mcd_utilisateurs) via la relation
        //    hasOne déjà définie dans User.php :
        //        public function utilisateur() {
        //            return $this->hasOne(Utilisateur::class, 'id', 'id');
        //        }
        //    $user->utilisateur()->create([...]) utilise cette relation pour
        //    savoir automatiquement quelle valeur mettre dans la colonne
        //    "id" de la nouvelle ligne mcd_utilisateurs : celle de $user->id.
        //    On n'écrit jamais cet id à la main.
        // =====================================================================
        $user = User::create([
            'name' => "{$data['prenom']} {$data['nom']}",
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $user->utilisateur()->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'classe' => $data['classe'] ?? null,
            'numero_candidat' => $data['numero_candidat'] ?? null,
            'id_role' => $data['id_role'],
            'code_statut' => $data['code_statut'],
            'code_genre' => $data['code_genre'],
        ]);

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé avec succès.');
    }

    // Formulaire de modification. $utilisateur est injecté automatiquement
    // par Laravel à partir de l'id dans l'URL (route model binding).
    public function edit(Utilisateur $utilisateur)
    {
        $this->ensureAdmin();

        // Précharge le compte de connexion, pour afficher l'email en
        // lecture seule dans la vue (voir Utilisateur::compte() ci-dessus).
        $utilisateur->load('compte');

        return view('admin.utilisateurs.edit', [
            'utilisateur' => $utilisateur,
            'roles' => Role::orderBy('nom')->get(),
            'statuts' => Statut::orderBy('nom')->get(),
            'genres' => Genre::orderBy('nom')->get(),
        ]);
    }

    // Enregistre la modification. Pas de champ email/password ici : on ne
    // gère pas encore le changement de mot de passe (ce sera une page à
    // part si besoin), et l'email n'est pas modifiable depuis cette page.
    public function update(Request $request, Utilisateur $utilisateur)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:50'],
            'prenom' => ['required', 'string', 'max:50'],
            'classe' => ['nullable', 'string', 'max:10'],
            'numero_candidat' => ['nullable', 'string', 'max:20'],
            'id_role' => ['required', 'exists:mcd_roles,id'],
            'code_statut' => ['required', 'exists:mcd_statuts,code'],
            'code_genre' => ['required', 'exists:mcd_genres,code'],
        ]);

        // Même protection que dans le composant Livewire GestionComptes :
        // l'administrateur connecté ne peut ni quitter le statut Actif, ni
        // s'enlever le rôle ADMIN, sinon il se bloquerait lui-même. Sans
        // ce contrôle ici aussi, la protection pourrait être contournée en
        // passant par ce formulaire "Modifier".
        if ((int) $utilisateur->id === (int) auth()->id()) {
            $roleChoisi = Role::find($data['id_role']);

            if ($data['code_statut'] !== 'A' || $roleChoisi?->code !== 'ADMIN') {
                return back()
                    ->withErrors(['code_statut' => 'Vous ne pouvez pas modifier votre propre statut ni votre propre rôle administrateur.'])
                    ->withInput();
            }
        }

        $utilisateur->update($data);

        // On garde le "name" du compte de connexion (mcd_users) synchronisé
        // avec nom/prenom, pour que "Bonjour ..." dans le menu reste juste
        // après un changement de nom/prénom.
        $utilisateur->compte->update([
            'name' => "{$data['prenom']} {$data['nom']}",
        ]);

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur modifié avec succès.');
    }
}
