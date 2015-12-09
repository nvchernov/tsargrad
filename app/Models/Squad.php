<?php
/**
 * Created by PhpStorm.
 * User: �����
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goal()
    {
        return $this->belongsTo('App\Models\Castle', 'goal_id');
    }

    /**
     * Получить награды отряда.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewards()
    {
        return $this->hasMany('App\Models\Reward');
    }

    /**
     * Получить все ресурсы, которые получил отряд при грабеже.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function resources()
    {
        return $this->belongsToMany('App\Models\Resource', 'rewards', 'squad_id', 'resource_id')
            ->withTimestamps()
            ->withPivot('count');
    }

    /**
     * Получить награбленные ресурсы или ресурс отряда.
     *
     * @param null $resource
     * @return \Illuminate\Support\Collection|int|null
     */
    public function getRewards($resource = null)
    {
        if (isset($resource)) {
            // Извлечь ресурс из БД...
            $res = Resource::extract($resource);
            if (isset($res)) {
                $count = 0; // Количество ресурса...
                // Если существует такой ресурс, то попробывать получить эту свзяь...
                $res = $this->resources()->find($res->id);
                if (isset($res)) {
                    $count = $res->pivot->count;
                }
                return $count;
            }
        } else {
            $arr = [];
            // Извлечь все ресурсы этого замка...
            foreach ($this->resources()->get() as $r) {
                $arr[] = ['name' => $r->name, 'count' => $r->pivot->count];
            }
            return collect($arr);
        }

        return null;
    }

    public function __get($key)
    {
        // Получить состояние отряда...
        if ($key == 'state') {
            $now = \Carbon\Carbon::now();

            $state = 'Отряд собирается напасть на замок';
            if (isset($this->crusade_end_at) && $now->gte($this->crusade_end_at)) {
                $state = "Отряд успешно вернулся из похода {$this->crusade_end_at->toDateTimeString()}";
            } elseif (isset($this->crusade_end_at) && $now->lt($this->crusade_end_at)) {
                $state = "Отряд вернется из похода {$this->crusade_end_at->toDateTimeString()}";
            } elseif ($now->eq($this->battle_at)) {
                $state = "Отряд сейчас сражается!";
            } elseif ($now->lt($this->battle_at) && $now->gte($this->crusade_at)) {
                $state = "Отряд вышел в поход {$this->crusade_at->toDateTimeString()}, " .
                    "Сражение состоится {$this->battle_at->toDateTimeString()}";
            }
            return $state;
        }

        return parent::__get($key);
    }
}