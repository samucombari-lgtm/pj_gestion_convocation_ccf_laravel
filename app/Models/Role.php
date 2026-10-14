<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'mcd_roles';

    // Colonnes autorisées dans Role::create([...]) (page Paramétrage).
    // "id" n'y est pas : c'est un auto-incrément, MySQL le génère.
    protected $fillable = ['code', 'nom', 'commentaire'];

    // hasMany : un rôle est porté par PLUSIEURS utilisateurs
    // (mcd_utilisateurs.id_role pointe vers mcd_roles.id).
    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class, 'id_role');
    }

    // Les actions que ce rôle a le droit de faire, via la table pivot
    // mcd_autoriser (symétrique de Permission::roles()).
    // belongsToMany(Modèle_visé, table_pivot, ma_clé_dans_le_pivot, clé_de_l'autre_dans_le_pivot)
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'mcd_autoriser', 'id_role', 'id_action');
    }
}
