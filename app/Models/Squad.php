<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 16:25
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes;

class Squad extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'squads';

    protected $dates = ['deleted_at', 'updated_at', 'created_at', 'crusade_at', 'crusade_end_at', 'battle_at'];

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

    public function __get($key)
    {
        $value = parent::__get($key);
        if (!isset($value)) {

            if ($key == 'state') {
                $now = \Carbon\Carbon::now();

                if (isset($this->crusade_end_at) && $now->gte($this->crusade_end_at)) {
                    $value = "Успешно вернулся из похода {$this->crusade_end_at->toDateTimeString()}";
                } elseif (isset($this->crusade_end_at) && $now->lt($this->crusade_end_at)) {
                    $value = "Вернется из похода {$this->crusade_end_at->toDateTimeString()}";
                } elseif ($now->eq($this->battle_at)) {
                    $value = "Сражается с противником";
                } elseif ($now->lt($this->battle_at) && $now->gte($this->crusade_at)) {
                    $value = "В походе от {$this->crusade_at->toDateTimeString()}, " .
                        "сражение состоится {$this->battle_at->toDateTimeString()}";
                }
            }
        }

        return $value;
    }
}