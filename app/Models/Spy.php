<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spy extends Model
{
    
    use SoftDeletes;
    
    protected $table = 'spy';
    
    protected $dates = ['deleted_at'];
    
    public function costUpgrade() {
        return round(exp($this->level/7) * 200);    
    }
    
    public function ownCastle() {
        return $this->belongsTo('App\Models\Castle', 'castles_id');
    }
    
    public function enemyCastle() {
        return $this->belongsTo('App\Models\Castle', 'enemy_castles_id');
    }
    
    public function levelUp() {
        $this->level +=  1;
        $this->save();
    }
    
    public function getEnemyCastleCoords() {
        return $this->enemyCastle()->first()->location()->first();
    }
    
    public function killMe() {
        $this->delete();
    }
    
    public function canDetectedAttack($attackLevel, $attackCount) {
        if (($this->level / $attackLevel) * $attackCount > 1) {
            return true;
        } else {
            return false;
        }
    }
    
}