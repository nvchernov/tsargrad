<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PveEnemyMessage extends Model
{
    protected $table = 'pve_enemy_messages';
    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
