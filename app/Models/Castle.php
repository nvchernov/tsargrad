<?php
/**
 * Created by PhpStorm.
 * User: Роман
 *
 * Date: 09.11.2015
 * Time: 16:21
 */

namespace App\Models;

use App\Events\CUD;
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

    /**
     * Casts.
     *
     * @var array
     */
    protected $casts = [
        //
        //    'location' => 'array',
    ];

    
    public function initResources() {
        $this->addResource('wood', 200);
        $this->addResource('gold', 200);
        $this->addResource('food', 200);
    }
    
    public function createBuildings() {
        // Лепим лесопилку
        $sawmill = new Building();
        $sawmill->castle()->associate($this);
        $sawmill->buildingType()->associate(BuildingType::where('building_name', 'sawmill')->first());
        $sawmill->level = 1;
        $sawmill->save();

        // Шахту
        $sawmill = new Building();
        $sawmill->castle()->associate($this);
        $sawmill->buildingType()->associate(BuildingType::where('building_name', 'mine')->first());
        $sawmill->level = 1;
        $sawmill->save();

        // Ферму
        $farm = new Building();
        $farm->castle()->associate($this);
        $farm->buildingType()->associate(BuildingType::where('building_name', 'farm')->first());
        $farm->level = 1;
        $farm->save();

        // Защитные сооружения
        $defences = new Building();
        $defences->castle()->associate($this);
        $defences->buildingType()->associate(BuildingType::where('building_name', 'defenses')->first());
        $defences->level = 0;
        $defences->save();
    }
    
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

    private function createResCond($res)
    {
        $cond = [];
        if (is_string($res)) {
            $cond['name'] = $res;
        } elseif ($res instanceof Resource) {
            $cond['id'] = $res->id;
        } elseif (is_integer($res)){
            $cond['id'] = $res;
        } else {
            return false;
        }
        return $cond;
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

        $cond = [];
        if (is_string($resource)) {
            $resName = $cond['name'] = $resource;
        } elseif ($resource instanceof Resource) {
            $resObj = $resource;
            $cond['id'] = $resource->id;
        } elseif (is_integer($resource)){
            $cond['id'] = $resource;
        } else {
            return false;
        }

        // связка с pivot...
        $rp = $this->resources()->where($cond)->first();
        if (isset($rp)) {
            // Увеличить ресурс...
            $rp->pivot->count += $count;
            $saved = $rp->pivot->save();
            if ($saved) {
                event(new CUD($this->user, 'update', $rp, ['name' => $rp->name, 'count' => $rp->pivot->count]));
            }
            return $saved;
        } elseif (!isset($resObj)) {
            // Есть такой ресурс в БД?
            $resObj = Resource::where($cond)->first();
            if (is_null($resObj) && isset($resName)) {
                // Если нет ресурса...
                // Создать новый ресурс.
                $resObj = Resource::create(['name' => $resName]);
            }
        }

        if (isset($resObj)) {
            // Добавить новый ресурс...
            $this->resources()->attach($resObj->id, ['count' => $count]);
            event(new CUD($this->user, 'update', $resObj, ['name' => $resObj->name, 'count' => $count]));

            return true;
        }

        return false;
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

        $cond = $this->createResCond($resource);
        if ($cond == false) {
            return false;
        }

        // связка с pivot...
        $rp = $this->resources()->where($cond)->first();
        if (is_null($rp)) {
            return false;
        }
        if ($rp->pivot->count - $count < 0) {
            throw new GameException('Не достаточно ресурсов.');
        }
        // Уменьшить ресурс...
        $rp->pivot->count -= $count;
        $saved = $rp->pivot->save();

        if ($saved) {
            event(new CUD($this->user, 'update', $rp, ['name' => $rp->name, 'count' => $rp->pivot->count]));
        }

        return $saved;
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
            $cond = $this->createResCond($resource);
            if ($cond == false) {
                return null;
            }
            // связка с pivot...
            $rp = $this->resources()->where($cond)->first();
            return !is_null($rp) ? $rp->pivot->count : 0;
        }
        $arr = [];
        // Извлечь все ресурсы этого замка...
        foreach ($this->resources()->getResults() as $r) {
            $arr[] = ['name' => $r->name, 'count' => $r->pivot->count];
        }
        return collect($arr);
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
    
    
    public function buildings() {
        
        return $this->hasMany('App\Models\Building');
        
    }
    
    public function fortification() {
        
        return Building::where('castles_id', $this->id)->where('buildings_id', 4)->first();
        
    }
}