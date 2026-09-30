<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 'mcd_genres';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    // Colonnes autorisées dans Genre::create([...]) (page Paramétrage).
    // Ici "code" y est, car c'est nous qui le choisissons (pas d'auto-incrément).
    protected $fillable = ['code', 'nom', 'commentaire'];
}
