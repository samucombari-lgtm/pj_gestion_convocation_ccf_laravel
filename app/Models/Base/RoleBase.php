<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe générée automatiquement par db2laravel.
 *
 * Ne pas modifier directement cette classe : elle peut être régénérée.
 */
abstract class RoleBase extends Model
{
    protected $table = 'mcd_roles';

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

    public function mcdUtilisateurs(): HasMany
    {
        return $this->hasMany(
            \App\Models\Utilisateur::class,
            'id_role',
            'id'
        );
    }
}
