<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Epreuve extends Model
{
    protected $table = 'mcd_epreuves';

    // Colonnes qu'on autorise à remplir via Epreuve::create([...]) - voir
    // Utilisateur.php pour l'explication de ce mécanisme de sécurité.
    protected $fillable = [
        'code', 'nom', 'date_debut', 'date_fin', 'duree_candidat',
        'commentaire', 'id_lieu',
    ];

    // Convertit automatiquement ces colonnes DATETIME en objets Carbon
    // (au lieu de simples chaînes de texte), pour pouvoir écrire par
    // exemple $epreuve->date_debut->translatedFormat('l d F Y') dans une vue.
    protected function casts(): array
    {
        return [
            'date_debut' => 'datetime',
            'date_fin' => 'datetime',
        ];
    }

    // belongsTo : mcd_epreuves.id_lieu pointe vers mcd_lieux.id
    public function lieu()
    {
        return $this->belongsTo(Lieu::class, 'id_lieu');
    }

    // Many-to-many via la table pivot mcd_affecter : les panels de jury
    // affectés à cette épreuve (symétrique de Jury::epreuves()).
    public function jurys()
    {
        return $this->belongsToMany(Jury::class, 'mcd_affecter', 'id_epreuve', 'id_jury');
    }

    // hasMany : toutes les lignes mcd_passer de cette épreuve, c'est-à-dire
    // tous les candidats qui la passent. On s'en sert pour détecter un
    // éventuel conflit d'intérêt (un membre du panel qui serait aussi
    // candidat sur cette épreuve).
    public function passages()
    {
        return $this->hasMany(Passer::class, 'id_epreuve');
    }
}
