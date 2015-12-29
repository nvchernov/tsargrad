<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spy extends Model
{
    protected $table = 'spy';
    
    public $timestamps = false;
    
    public function costUpgrade() {
        return round(exp($this->level/7) * 200);    
    }
    
    public function ownCastle() {
        return $this->belongsTo('App\Models\Castle', 'castles_id');
    }
    
    public function enemyCastle() {
        return $this->belongsTo('App\Models\Castle', 'enemy_castles_id');
    }
}
