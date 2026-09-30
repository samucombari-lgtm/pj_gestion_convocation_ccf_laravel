<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe générée automatiquement par db2laravel.
 *
 * Ne pas modifier directement cette classe : elle peut être régénérée.
 */
abstract class LieuBase extends Model
{
    protected $table = 'mcd_lieux';

    protected $fillable = [
        'code',
        'nom',
        'adresse',
        'commentaire',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function mcdEpreuves(): HasMany
    {
        return $this->hasMany(
            \App\Models\Epreuve::class,
            'id_lieu',
            'id'
        );
    }
}
