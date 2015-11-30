<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 16:24
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Army extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'armies';

    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    /**
     * Get all squads.
     * One to Many relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function squads()
    {
        return $this->hasMany('App\Models\Squad');
    }

    /**
     * Get a castle.
     * One to One relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function castle()
    {
        return $this->belongsTo('App\Models\Castle');
    }

    /**
     * Magic getter!
     *
     * @param string $key
     * @return int|mixed
     */
    public function __get($key)
    {
        $value = parent::__get($key);
        if (!isset($value)) {
            // Get a size of all squads of army.
            if ($key == 'sizeSquads') {
                $value = 0;
                foreach ($this->squads as $s) {
                    $value += $s->size;
                }
            }
            // Get a size of a the army with all squads.
            if ($key == 'strength') {
                $value = $this->size + $this->sizeSquads;
            }
        }

        return $value;
    }
}