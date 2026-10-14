<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Par défaut Laravel cherche une table "users" : on lui dit d'utiliser
    // la vraie table de ton MCD.
    protected $table = 'mcd_users';

    protected $fillable = ['name', 'email', 'password'];

    // Ne jamais renvoyer ces colonnes si on affiche l'utilisateur en JSON
    protected $hidden = ['password', 'remember_token'];

    // Dit à Laravel de hacher automatiquement le mot de passe dès qu'on
    // fait $user->password = '...' puis $user->save() (utile plus tard,
    // par exemple quand un gestionnaire créera un compte candidat).
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // mcd_utilisateurs.id EST le même id que mcd_users.id (clé partagée,
    // on l'avait vu en lisant le script de création). On le précise ici :
    // hasOne(Modèle, clé_étrangère_sur_l'autre_table, clé_locale_ici)
    public function utilisateur()
    {
        return $this->hasOne(Utilisateur::class, 'id', 'id');
    }

    // L'utilisateur connecté (auth()->user()) est un User (mcd_users),
    // mais le rôle est porté par sa fiche Utilisateur (mcd_utilisateurs).
    // On délègue donc la question à Utilisateur::hasPermission().
    // Un compte sans fiche Utilisateur n'a aucune permission (false).
    // C'est cette méthode qu'appellent les Gates définies dans
    // AppServiceProvider (middleware can:, @can, Gate::authorize).
    public function hasPermission(string $permission): bool
    {
        return $this->utilisateur?->hasPermission($permission) ?? false;
    }
}
