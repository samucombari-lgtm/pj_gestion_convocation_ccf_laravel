<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passer extends Model
{
    protected $table = 'mcd_passer';

    // mcd_passer n'a pas de colonne "id" : sa clé primaire est la PAIRE
    // (id_utilisateur, id_epreuve) - un candidat ne peut pas passer deux
    // fois la même épreuve. Eloquent ne sait pas gérer nativement une clé
    // primaire composée de 2 colonnes, mais ce n'est pas un problème ici
    // car on ne fait que LIRE ces lignes (pas de $passer->save()).
    protected function casts(): array
    {
        return [
            'horaire' => 'datetime',
        ];
    }

    // belongsTo : mcd_passer.id_epreuve pointe vers mcd_epreuves.id
    public function epreuve()
    {
        return $this->belongsTo(Epreuve::class, 'id_epreuve');
    }

    // belongsTo : mcd_passer.id_jury pointe vers mcd_jurys.id
    public function jury()
    {
        return $this->belongsTo(Jury::class, 'id_jury');
    }

    // belongsTo : mcd_passer.id_utilisateur pointe vers mcd_utilisateurs.id
    // (utile plus tard pour la page JURY : "mes candidats à évaluer")
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
