<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe générée automatiquement par db2laravel.
 *
 * Ne pas modifier directement cette classe : elle peut être régénérée.
 */
abstract class EpreuveBase extends Model
{
    protected $table = 'mcd_epreuves';

    protected $fillable = [
        'code',
        'nom',
        'date_debut',
        'date_fin',
        'duree_candidat',
        'commentaire',
        'id_lieu',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'date_debut' => 'datetime',
            'date_fin' => 'datetime',
            'duree_candidat' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'id_lieu' => 'integer',
        ];
    }

    public function lieu(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Lieu::class,
            'id_lieu',
            'id'
        );
    }

    public function mcdPasser(): HasMany
    {
        return $this->hasMany(
            \App\Models\Passer::class,
            'id_epreuve',
            'id'
        );
    }
}
