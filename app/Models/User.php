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

    // Petit raccourci pratique qu'on va utiliser partout dans les vues
    public function hasRole(string $code): bool
    {
        return $this->utilisateur && $this->utilisateur->role->code === $code;
    }

    // Décision du professeur : l'ancien rôle JURY est remplacé par deux
    // rôles, JURY_ENS (jury enseignant) et JURY_PRO (jury professionnel).
    // Les pages jury sont les mêmes pour les deux : plutôt que d'écrire
    // hasRole('JURY_ENS') || hasRole('JURY_PRO') à chaque endroit (et
    // risquer d'en oublier un), on regroupe le test ici, une seule fois.
    public function isJury(): bool
    {
        return $this->hasRole('JURY_ENS') || $this->hasRole('JURY_PRO');
    }
}
