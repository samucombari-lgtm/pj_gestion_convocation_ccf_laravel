<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statut extends Model
{
    protected $table = 'mcd_statuts';

    // Ici la clé primaire ne s'appelle pas "id" (comme Laravel s'y attend
    // par défaut) mais "code" : on le précise.
    protected $primaryKey = 'code';

    // "code" n'est pas un nombre auto-incrémenté, c'est un CHAR(1) fixé
    // à la main ('A', 'I', 'B') : on désactive l'auto-incrémentation.
    public $incrementing = false;

    // Et on précise que le type de cette clé est du texte, pas un entier.
    protected $keyType = 'string';
}
