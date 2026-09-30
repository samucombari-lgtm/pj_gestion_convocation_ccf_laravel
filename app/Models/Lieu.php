<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lieu extends Model
{
    protected $table = 'mcd_lieux';

    // Colonnes qu'on autorise à remplir via Lieu::create([...]).
    protected $fillable = ['code', 'nom', 'adresse', 'commentaire'];
}
