<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 'mcd_genres';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
}
