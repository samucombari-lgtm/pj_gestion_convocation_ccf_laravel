<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe générée automatiquement par db2laravel.
 *
 * Ne pas modifier directement cette classe : elle peut être régénérée.
 */
abstract class JuryBase extends Model
{
    protected $table = 'mcd_jurys';

    protected $fillable = [
        'code',
        'nom',
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

    public function mcdPasser(): HasMany
    {
        return $this->hasMany(
            \App\Models\Passer::class,
            'id_jury',
            'id'
        );
    }
}
