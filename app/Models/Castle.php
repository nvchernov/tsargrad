<?php
/**
 * Created by PhpStorm.
 * User: Роман
 *
 * Date: 09.11.2015
 * Time: 16:21
 */

namespace App\Models;

use App\Exceptions\GameException;
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

    protected $fillable = ['name'];

    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    public static function boot()
    {
        parent::boot();

        static::created(function(Castle $castle)
        {
            // Задать позицию на карте и армию по-умолчанию.
            $loc = Location::freeRandom();
            if (is_null($loc)) {
                throw new GameException('Нельзя добавить новый замок. Все поле уже занято.');
            }
            $loc->castle()->associate($castle);
            $loc->save();

            $castle->army()->create(['name' => "{$castle->name}'s army", 'size' => 0, 'level' => 1]);
        });
    }

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
     * Получить армию или создать новую....
     */
    public function armyOrCreate()
    {
        $army = $this->army()->getResults();
        if (is_null($army)) {
            $army = $this->army()->create(['name' => "{$this->name}'s army", 'size' => 0, 'level' => 1]);
        }
        return $army;
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
        $res = Resource::extract($resource);
        if (isset($res)) {
            // Если существует такой ресурс, то попробывать получить эту свзяь...
            $r = $this->resources()->find($res->id);
            if (isset($r)) {
                // Увеличить ресурс...
                $r->pivot->count += $count;
                return $r->pivot->save();
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
     * @throws GameException
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

        // Извлечь ресурс...
        $res = Resource::extract($resource);
        if (is_null($res)) {
            return false;
        }

        // Если существует такой ресурс, то попробывать получить эту свзяь...
        $res = $this->resources()->find($res->id);
        if (is_null($res)) {
            return false;
        }

        if ($res->pivot->count - $count < 0) {
            throw new GameException('Не достаточно ресурсов.');
        }
        // Уменьшить ресурс...
        $res->pivot->count -= $count;
        return $res->pivot->save();
    }

    /**
     * Извлечь все ресурсы или ресурс замка.
     *
     * @param Resource|string|int|null $resource
     * @return int|\Illuminate\Support\Collection|null
     */
    public function getResources($resource = null)
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
            foreach ($this->resources()->getResults() as $r) {
                $arr[$r->name] = $r->pivot->count;
            }
            return collect($arr);
        }
        // Если такого ресурса нет в БД...
        return null;
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
     * One to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attackers()
    {
        return $this->hasMany('App\Models\Squad', 'goal_id');
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

    public function location()
    {
        return $this->hasOne('App\Models\Location');
    }

    /**
     * Get user.
     * One to One relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}