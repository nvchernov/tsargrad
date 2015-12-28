<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingType extends Model
{
    protected $table = 'buildings';
    
    public $timestamps = false;
    
    public function resource() {
        
        return $this->belongsTo('App\Models\Resource', 'resources_id');
        
    }     
}
