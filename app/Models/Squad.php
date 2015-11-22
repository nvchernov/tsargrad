<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 16:25
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Squad extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'squads';

    protected $dates = [
        'deleted_at', 'updated_at', 'created_at', 'crusade_at', 'crusade_end_at', 'battle_at'
    ];

    /**
     * Get an army.
     * One to Many relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function army()
    {
        return $this->belongsTo('App\Models\Army');
    }

    /**
     * Get a goal of crusade.
     * Morphing.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function crusadeable()
    {
        return $this->morphTo();
    }
}