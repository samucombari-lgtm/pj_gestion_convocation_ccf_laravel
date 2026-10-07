<?php

namespace App\Actions\Fortify;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

// Action appelée par Fortify quand le formulaire d'inscription (POST
// /register) est envoyé. Fortify s'occupe de la route et du contrôleur ;
// nous, on décide ICI quels champs sont demandés et comment le compte est
// créé dans NOTRE base (mcd_users + mcd_utilisateurs).
class CreateNewUser implements CreatesNewUsers
{
    // Règles du mot de passe (8 caractères minimum + confirmation),
    // définies dans PasswordValidationRules.php.
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input  les champs envoyés par le formulaire
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        // Champs demandés. Les colonnes OBLIGATOIRES (NOT NULL) du script de
        // création sont : mcd_users.name/email/password et
        // mcd_utilisateurs.nom/prenom/id_role/code_statut/code_genre.
        // La classe est facultative (colonne qui accepte NULL).
        // Les tailles max reprennent celles des colonnes (nom VARCHAR(50)...).
        // Si une règle n'est pas respectée, validate() renvoie
        // automatiquement au formulaire avec les messages d'erreur (en
        // français grâce à lang/fr/validation.php).
        Validator::make($input, [
            'nom' => ['required', 'string', 'max:50'],
            'prenom' => ['required', 'string', 'max:50'],
            'code_genre' => ['required', 'exists:mcd_genres,code'],
            'classe' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:mcd_users,email'],
            'password' => $this->passwordRules(),
        ], [
            'email.unique' => 'Cette adresse e-mail est déjà utilisée par un autre compte.',
        ])->validate();

        // Le rôle et le statut ne viennent JAMAIS du formulaire : même si
        // quelqu'un ajoute un champ "id_role" à la main dans la page, on ne
        // le lit pas. Toute personne qui s'inscrit est un candidat (CAND),
        // Inactif ('I') tant qu'un administrateur ne l'a pas validé.
        $roleCandidat = Role::where('code', 'CAND')->firstOrFail();

        // DB::transaction : les 2 insertions réussissent ensemble ou sont
        // annulées ensemble. Sans ça, si la 2e échouait, il resterait un
        // compte mcd_users sans fiche mcd_utilisateurs (donc sans rôle).
        return DB::transaction(function () use ($input, $roleCandidat) {
            // 1) Le compte de connexion (mcd_users). Le mot de passe est
            //    haché automatiquement grâce au cast 'hashed' de User.php.
            $user = User::create([
                'name' => "{$input['prenom']} {$input['nom']}",
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            // 2) La fiche métier (mcd_utilisateurs), créée via la relation
            //    utilisateur() : Laravel y met tout seul le MÊME id que
            //    $user (clé partagée), on ne l'écrit jamais à la main.
            $user->utilisateur()->create([
                'nom' => $input['nom'],
                'prenom' => $input['prenom'],
                'classe' => ($input['classe'] ?? '') !== '' ? $input['classe'] : null,
                'id_role' => $roleCandidat->id,
                'code_statut' => 'I',
                'code_genre' => $input['code_genre'],
            ]);

            return $user;
        });
    }
}
