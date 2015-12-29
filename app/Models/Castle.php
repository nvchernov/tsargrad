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
        if (!(is_numeric($count) && $count != 0)) {
            return false;
        }

        // Вычитаем...
        if ($count < 0) {
            return $this->subResource($resource, abs($count));
        }

        $res = Resource::extract($resource);
        if (isset($res)) {
            // связка с pivot...
            $rp = $this->resources()->find($res->id);
            if (isset($rp)) {
                // Увеличить ресурс...
                $rp->pivot->count += $count;
                $saved = $rp->pivot->save();
                if ($saved) {
                    event(new CUD($this->user, 'update', $rp, ['name' => $rp->name, 'count' => $rp->pivot->count]));
                }
                return $saved;
            }
        } else {
            if (is_string($resource)) {
                // Если нет ресурса...
                // Создать новый ресурс.
                $res = Resource::create(['name' => $resource]);
            }
        }

        if (isset($res)) {
            // Добавить новый ресурс...
            $this->resources()->attach($res->id, ['count' => $count]);
            event(new CUD($this->user, 'update', $res, ['name' => $res->name, 'count' => $count]));

            return true;
        }

        return false;
    }
    
    public function calcCastleIncreaseResources() {
        
        foreach($this->resources()->getResults() as $res) {            
            $lastUpdateTime = $res->pivot->updated_at;            
            $nowTime = \Carbon\Carbon::now();
            $build = $this->getBuildingFromResName($res->name);
            $needAddRes = $nowTime->diffInSeconds($lastUpdateTime) * $build->level;
            $this->addResource($res->name, $needAddRes);
        };
        
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
        if (!(is_numeric($count) && $count != 0)) {
            return false;
        }

        // Вычитаем...
        if ($count < 0) {
            return $this->addResource($resource, abs($count));
        }

        $res = Resource::extract($resource);
        if (is_null($res)) {
            return false;
        }

        // связка с pivot...
        $rp = $this->resources()->find($res->id);
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
            $res = Resource::extract($resource);
            if (is_null($res)) {
                return null;
            }
            // связка с pivot...
            $rp = $this->resources()->find($res->id);
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
        
        return $this->hasMany('App\Models\Building', 'castles_id');
        
    }
    
    public function fortification() {
        
        return Building::where('castles_id', $this->id)->where('buildings_id', 4)->first();
        
    }
    
    
    public function getBuildingFromResName($resName) {
        return $this->buildings()->where('buildings_id', BuildingType::where('resources_id', 
                Resource::where('name', $resName)->first()->id)->first()->id)->first();
    }
    
    public function ownSpies() {
        
        return $this->hasMany('App\Models\Spy', 'castles_id');
        
    }
    
}