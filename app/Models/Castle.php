<?php
/**
 * Created by PhpStorm.
 * User: Роман
 *
 * Date: 09.11.2015
 * Time: 16:21
 */

namespace App\Models;

use App\Exceptions\GameExecption;
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
     * Извлечь ресурс из БД если это возможно.
     *
     * @param $obj
     * @return Resource|null
     */
    protected function extractResource($obj)
    {
        $res = null;
        if ($obj instanceof Resource) {
            $res = $obj;
        } elseif (is_string($obj)) {
            $res = Resource::where(['name' => $obj])->first();
        } elseif (is_integer($obj)) {
            $res = Resource::find($obj);
        }
        return $res;
    }

    /**
     * Добавить некоторое количество ресурса в замок.
     * Если такого ресурса еще не было в замке, то сначала создается новый ресурс в БД, а затем он появляется и в замке.
     *
     * @param Resource|string|int $resource
     * @param $count
     * @return bool
     */
    public function addResource($resource, $count)
    {
        // Нет пустой работе...
        if (!(is_integer($count) && $count != 0)) {
            return false;
        }

        // Вычитаем...
        if ($count < 0) {
            return $this->subResource($resource, abs($count));
        }
        // Попытаться извлечь ресурс...
        $res = $this->extractResource($resource);
        if (isset($res)) {

            // Если существует такой ресурс, то попробывать получить эту свзяь...
            $r = $this->resources()->find($res->id);
            if (isset($r)) {
                // Увеличить ресурс...
                $r->pivot->count += $count;
                $r->pivot->save();
                return true;
            }
        } else {
            // Если нет ресурса...
            // И нет возможности его создать...
            if (!is_string($resource)) {
                return false;
            }
            // Создать новый ресурс.
            $res = Resource::create(['name' => $resource]);
        }

        // Добавить новый ресурс...
        $this->resources()->attach($res->id, ['count' => $count]);

        return true;
    }

    /**
     * Уменьшить ресурс на указанное число. Если это невозможно, то выкидывается исключение.
     *
     * @param Resource|string|int $resource
     * @param $count
     * @return bool
     * @throws GameExecption
     */
    public function subResource($resource, $count)
    {
        // Нет пустой работе...
        if (!(is_integer($count) && $count != 0)) {
            return false;
        }

        // Вычитаем...
        if ($count < 0) {
            return $this->addResource($resource, abs($count));
        }

        $res = $this->extractResource($resource);
        if (!isset($res)) {
            return false;
        }

        // Если существует такой ресурс, то попробывать получить эту свзяь...
        $res = $this->resources()->find($res->id);
        if (!isset($res)) {
            return false;
        }

        if ($res->pivot->count - $count < 0) {
            throw new GameExecption('Not enough resources...');
        }
        // Уменьшить ресурс...
        $res->pivot->count -= $count;
        $res->pivot->save();

        return true;
    }

    /**
     * Извлечь все ресурсы или ресурс замка.
     *
     * @param Resource|string|int|null $resource
     * @return array|\Illuminate\Support\Collection|bool
     */
    public function getResources($resource = null)
    {
        if (isset($resource)) {
            // Извлечь ресурс из БД...
            $res = $this->extractResource($resource);
            if (isset($res)) {
                $arr = ['name' => $res->name, 'count' => 0];

                // Если существует такой ресурс, то попробывать получить эту свзяь...
                $res = $this->resources()->find($res->id);
                if (isset($res)) {
                    $arr['count'] = $res->pivot->count;
                }
                return $arr;
            }
        } else {
            $arr = [];
            // Извлечь все ресурсы этого замка...
            foreach ($this->resources()->get() as $r) {
                $arr[] = ['name' => $r->name, 'count' => $r->pivot->count];
            }
            return collect($arr);
        }

        return false;
    }

    /**
     * Get resources...
     * Many to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function resources()
    {
        return $this->belongsToMany('App\Models\Resource', 'scores', 'castle_id', 'resource_id')
            ->withTimestamps()
            ->withPivot('count');
    }

    /**
     * Get scores...
     * One to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scores()
    {
        return $this->hasMany('App\Models\Score');
    }

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