<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $table = 'buildings_castles';
    
    public $timestamps = false;
    
    public function castle() {
        return $this->belongsTo('App\Models\Castle', 'castles_id');
    }
    
    public function buildingType() {
        return $this->belongsTo('App\Models\BuildingType', 'buildings_id');
    }
    
    public function costUpdate() {
        return round(exp($this->level/2)*100);
    }
    
    public function levelUp() {
        $this->level +=  1;
        $this->save();
    }
    
}