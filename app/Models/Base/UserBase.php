<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe générée automatiquement par db2laravel.
 *
 * Ne pas modifier directement cette classe : elle peut être régénérée.
 */
abstract class UserBase extends Model
{
    protected $table = 'mcd_users';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function utilisateur(): HasOne
    {
        return $this->hasOne(
            \App\Models\Utilisateur::class,
            'id',
            'id'
        );
    }
}
