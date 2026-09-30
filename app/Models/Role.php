<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'mcd_roles';

    // Colonnes autorisées dans Role::create([...]) (page Paramétrage).
    // "id" n'y est pas : c'est un auto-incrément, MySQL le génère.
    protected $fillable = ['code', 'nom', 'commentaire'];
}
