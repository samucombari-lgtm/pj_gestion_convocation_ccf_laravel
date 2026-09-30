<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe générée automatiquement par db2laravel.
 *
 * Ne pas modifier directement cette classe : elle peut être régénérée.
 */
abstract class PasserBase extends Model
{
    protected $table = 'mcd_passer';

    // ATTENTION : clé primaire composite détectée : id_utilisateur, id_epreuve

    // Eloquent ne gère pas nativement les clés primaires composites.

    public $incrementing = false;

    protected $fillable = [
        'id_utilisateur',
        'id_epreuve',
        'horaire',
        'id_jury',
    ];

    protected function casts(): array
    {
        return [
            'id_utilisateur' => 'integer',
            'id_epreuve' => 'integer',
            'horaire' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'id_jury' => 'integer',
        ];
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Utilisateur::class,
            'id_utilisateur',
            'id'
        );
    }

    public function epreuve(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Epreuve::class,
            'id_epreuve',
            'id'
        );
    }

    public function jury(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Jury::class,
            'id_jury',
            'id'
        );
    }
}
