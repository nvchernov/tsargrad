<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 30.11.2015
 * Time: 23:16
 */

namespace app\Models;

use Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'resources';

    protected $fillable = ['name'];

    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    /**
     * Get scores for the resource...
     * One to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scores()
    {
        return $this->hasMany('App\Models\Score');
    }

    /**
     * Get rewards for the resource...
     * One to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewards()
    {
        return $this->hasMany('App\Models\Reward');
    }

    /**
     * Get castles...
     * Many to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function castles()
    {
        return $this->belongsToMany('App\Models\Castle', 'scores', 'resource_id', 'castle_id')
            ->withTimestamps()
            ->withPivot('count');
    }
}