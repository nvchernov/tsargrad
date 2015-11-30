<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 16:21
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes;

class Castle extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'castles';

    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    /**
     * Casts.
     *
     * @var array
     */
    protected $casts = [
    //
    //    'location' => 'array',
    ];

    /**
     * Get all enemies attacking the castle.
     * One to Many relation via Morphing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attackers()
    {
        return $this->morphMany('App\Models\Squad', 'crusadeable');
    }

    /**
     * Get all squads.
     * One to Many relation via Through.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function squads()
    {
        return $this->hasManyThrough('App\Models\Squad', 'App\Models\Army');
    }

    /**
     * Get army.
     * One to One relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function army()
    {
        return $this->hasOne('App\Models\Army');
    }
}