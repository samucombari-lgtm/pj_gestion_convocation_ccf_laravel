<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    protected $table = 'mcd_utilisateurs';

    // Cette table n'a PAS d'auto-incrémentation : son id vient de
    // mcd_users (clé partagée). Sans cette ligne, Eloquent essaierait
    // (à tort) de générer lui-même un nouvel id lors d'un insert.
    public $incrementing = false;

    // Les colonnes qu'on autorise à remplir via Utilisateur::create([...])
    // ou $utilisateur->fill([...]). C'est une sécurité : sans ça, Laravel
    // refuse de remplir un champ qui n'est pas dans cette liste.
    protected $fillable = [
        'nom', 'prenom', 'classe', 'date_naissance', 'adresse',
        'tel_mobile', 'numero_candidat', 'commentaire',
        'id_role', 'code_statut', 'code_genre',
    ];

    // ----- Relations vers les tables de référence -----

    // belongsTo = "cette table pointe vers l'autre" via une clé étrangère.
    // Ici : mcd_utilisateurs.id_role pointe vers mcd_roles.id
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    // Ici la clé étrangère (code_statut) ne s'appelle pas comme la clé
    // primaire visée (code) : on le précise en 2e et 3e paramètre.
    // belongsTo(Modèle, ma_colonne_ici, colonne_visée_chez_l'autre)
    public function statut()
    {
        return $this->belongsTo(Statut::class, 'code_statut', 'code');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'code_genre', 'code');
    }

    // hasMany et PAS hasOne : un même candidat peut avoir PLUSIEURS
    // convocations dans mcd_passer (une pour l'E5, une pour l'E6, et
    // éventuellement une pour un rattrapage). hasOne ne renverrait que
    // la première ligne trouvée et masquerait les autres convocations ;
    // hasMany renvoie une Collection de toutes les lignes qui pointent
    // vers cet utilisateur via id_utilisateur.
    public function convocations()
    {
        return $this->hasMany(Passer::class, 'id_utilisateur');
    }

    // belongsToMany et PAS hasMany : ici, mcd_former ne fait QUE relier
    // deux tables entre elles (id_utilisateur, id_jury), sans autre
    // colonne utile - c'est une vraie "table pivot" many-to-many :
    // - un membre de jury peut appartenir à PLUSIEURS panels (ex. Amelie
    //   Girard est dans le panel J1 ET le panel J3, si elle couvre 2
    //   sessions d'épreuves différentes) ;
    // - un panel contient PLUSIEURS membres (2 dans notre jeu de test).
    // Avec hasMany, Passer::class ci-dessus, la relation nous donne les
    // LIGNES de la table liée (mcd_passer). Avec belongsToMany, Laravel
    // fait le JOIN via mcd_former tout seul et nous donne directement une
    // Collection de Jury (les panels), pas les lignes de mcd_former
    // elles-mêmes : c'est ce qu'on veut, on ne se sert jamais de la ligne
    // pivot en tant que telle.
    // belongsToMany(Modèle_visé, table_pivot, ma_clé_dans_le_pivot, clé_de_l'autre_dans_le_pivot)
    public function jurys()
    {
        return $this->belongsToMany(Jury::class, 'mcd_former', 'id_utilisateur', 'id_jury');
    }

    // belongsTo : symétrique de User::utilisateur() (qui va de mcd_users
    // vers mcd_utilisateurs). Ici on fait l'inverse : depuis une fiche
    // mcd_utilisateurs, retrouver son compte de connexion mcd_users - même
    // id des deux côtés (clé partagée), donc belongsTo(User::class, 'id', 'id').
    // Sert par exemple à afficher l'email (lecture seule) sur la page de
    // modification d'un utilisateur.
    public function compte()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
