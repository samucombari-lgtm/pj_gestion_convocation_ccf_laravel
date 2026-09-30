<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe générée automatiquement par db2laravel.
 *
 * Ne pas modifier directement cette classe : elle peut être régénérée.
 */
abstract class UtilisateurBase extends Model
{
    protected $table = 'mcd_utilisateurs';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'nom',
        'prenom',
        'classe',
        'date_naissance',
        'adresse',
        'tel_mobile',
        'numero_candidat',
        'commentaire',
        'id_role',
        'code_statut',
        'code_genre',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'date_naissance' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'id_role' => 'integer',
        ];
    }

    public function id(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\User::class,
            'id',
            'id'
        );
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Role::class,
            'id_role',
            'id'
        );
    }

    public function codeStatut(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Statut::class,
            'code_statut',
            'code'
        );
    }

    public function codeGenre(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Genre::class,
            'code_genre',
            'code'
        );
    }

    public function mcdPasser(): HasMany
    {
        return $this->hasMany(
            \App\Models\Passer::class,
            'id_utilisateur',
            'id'
        );
    }
}
