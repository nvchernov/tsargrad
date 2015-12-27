<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PveEnemy extends Model
{
    protected $table = 'pve_enemies';
    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
