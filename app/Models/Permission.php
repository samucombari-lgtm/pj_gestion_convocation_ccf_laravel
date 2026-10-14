<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Une Permission = une action que l'on peut autoriser à un rôle, nommée
// au format Laravel "ressource.action" (ex. epreuves.create).
// Le modèle s'appelle Permission (comme dans le document du professeur)
// mais sa table s'appelle mcd_actions dans notre MCD.
class Permission extends Model
{
    protected $table = 'mcd_actions';

    protected $fillable = ['nom', 'commentaire'];

    // belongsToMany : une action peut être autorisée à PLUSIEURS rôles,
    // et un rôle a PLUSIEURS actions, via la table pivot mcd_autoriser.
    // On donne explicitement la table pivot et les 2 clés : sans elles,
    // Laravel chercherait une table "permission_role" qui n'existe pas.
    // belongsToMany(Modèle_visé, table_pivot, ma_clé_dans_le_pivot, clé_de_l'autre_dans_le_pivot)
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'mcd_autoriser', 'id_action', 'id_role');
    }
}
