<?php
/**
 * Created by PhpStorm.
 * User: �����
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
        // Получить состояния всех отрядов армии...
        if ($key == 'squadsStates') {
            $states = [];
            foreach ($this->squads()->get() as $s) { $states[$s->id] = $s->state; }
            return collect($states);
        }

        // Получить размер всех отрядов армии...
        if ($key == 'squadsSize') {
            $size = 0;
            foreach ($this->squads()->get() as $s) { $size += $s->size; }
            return $size;
        }

        // Получить силу армии...
        if ($key == 'strength') {
            return $this->size + $this->squadsSize;
        }

        return parent::__get($key);
    }
}