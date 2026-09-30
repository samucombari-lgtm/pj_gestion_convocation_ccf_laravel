<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jury extends Model
{
    protected $table = 'mcd_jurys';

    // Un panel (ex. "Jury n°1") peut être affecté à PLUSIEURS épreuves,
    // et une épreuve peut avoir PLUSIEURS panels si elle a beaucoup de
    // candidats (voir test_500.sql : E5S2 est couverte par 2 panels pour
    // tenir dans la journée). C'est donc aussi une relation many-to-many,
    // via la table pivot mcd_affecter.
    public function epreuves()
    {
        return $this->belongsToMany(Epreuve::class, 'mcd_affecter', 'id_jury', 'id_epreuve');
    }

    // Many-to-many via mcd_former : les personnes qui composent ce panel
    // (symétrique de Utilisateur::jurys()).
    public function membres()
    {
        return $this->belongsToMany(Utilisateur::class, 'mcd_former', 'id_jury', 'id_utilisateur');
    }
}
