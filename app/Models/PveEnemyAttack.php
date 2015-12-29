<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PveEnemyAttack extends Model
{
    protected $table = 'pve_enemy_attacks';

    public function enemy()
    {
        return $this->belongsTo('App\Models\PveEnemy','pve_enemy_id')->get()->first();
    }

    public static function findLastByUser($user_id)
    {
        return User::find($user_id)->lastPveAttack();
    }

    public function resource()
    {
        return $this->belongsTo('App\Models\Resource','demanded_resource_id')->get()->first();
    }
}
