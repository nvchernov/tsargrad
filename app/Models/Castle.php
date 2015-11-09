<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 16:21
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Castle extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'castles';

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
     * Get all armies.
     * One to Many relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function armies()
    {
        return $this->hasMany('App\Models\Army');
    }
}