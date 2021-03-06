<?php
/**
 * Created by PhpStorm.
 * User: rudnev
 * Date: 09.11.2015
 * Time: 16:25
 */

namespace App\Models;

use App\Events\SquadAssaulted;
use App\Events\SquadDisbanded;
use App\Events\CUD;
use App\Exceptions\GameException;
use Carbon\Carbon;
use DB;
use Log;
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

    protected $fillable = ['name', 'size'];

    protected $dates = ['deleted_at', 'updated_at', 'created_at', 'crusade_at', 'crusade_end_at', 'battle_at'];

    public function scopeReadyToDisband($query)
    {
        return $query->whereNotNull('crusade_end_at')->where('crusade_end_at', '<=', Carbon::now());
    }

    public function scopeReadyToAssault($query)
    {
        return $query->whereNull('crusade_end_at')->where('battle_at', '<=', Carbon::now());
    }

    public function save(array $options = [])
    {
        $exists = $this->exists;
        $saved = parent::save($options);

        if ($saved) {
            event(new CUD($this->user, $exists ? 'update' : 'create', $this));
        }

        return $saved;
    }

    public function delete()
    {
        $id = $this->id;
        $deleted = parent::delete();

        if ($deleted) {
            event(new CUD($this->user, 'delete', $this, ['id' => $id]));
        }

        return $deleted;
    }

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
     * Проверить на возможность продолжение или выбросить исключение
     * @throws GameException
     */
    private function continueOrException()
    {
        $army = $this->army;
        if (is_null($army)) {
            throw new GameException('Отряд не может существовать без армии.');
        }
        $goal = $this->goal;
        if (is_null($goal)) {
            throw new GameException('Отряду некого грабить. Не указан вражеский замок.');
        }
        if ($this->size == 0) {
            throw new GameException('В отряде нет людей.');
        }
    }

    /**
     * Штурмовать вражеский замок.
     * 
     * @throws GameException
     * @throws \Exception
     */
    public function assault()
    {
        $this->continueOrException();

        $now = Carbon::now();
        
        // Пересчет ресурсов
        $this->goal->calcCastleIncreaseResources();
        
        // Расчет победителя сражения...
        $aArmy = $this->army; // атакующая армия
        $dArmy = $this->goal->armyOrCreate(); // защищающиеся армия

        Log::info('---------------------------------------------------------------------------------------------------');
        Log::info("($now) Штурм замка id={$this->goal->id} '{$this->goal->name}' отрядом id={$this->id} '{$this->name}'...");

        // xa, ya - кол-во и уровень атакующих.
        // xd, yd, zd - кол-во, уровень войск и уровень фортификации защищающихся.
        $xa = $this->size;
        $ya = $aArmy->level;
        $xd = $dArmy->size;
        $yd = $dArmy->level;
        $zd = $this->goal->fortification()->level; // Уровень фортификации, пока остается 0;

        Log::info("Сила атак. отряда = $xa и уровень = $ya");
        Log::info("Сила защ. армии = $xd, уровень = $yd и фортифмкация = $zd");

        // Участие шпионов во всем этом замесе
        // Получаем шпионов замка
        $spiesHg = $this->goal->ownSpies()->getResults();
        //Log::info($spiesHg->toJson());
        foreach($spiesHg as $oneSpiesHg) {
            // С вероятностью 50/50 шпион погибает
            $rand = mt_rand(0,1);
            if($rand) {
                $oneSpiesHg->killMe();
            }
        }

        // Ищем шпиона, который проглядел нападение (если есть)
        $sph = SpyHistory::where('squads_id', $this->id)->where('detect', 0)->get()->all();
        foreach($sph as $oneSph) {
            $spy = Spy::find($oneSph->spy_id);
            if(!empty($spy)) {
                $spy->killMe();
            }
        }

        if ($xd == 0) {
            // Защитников нет, досрочная победа атакующих...
            DB::beginTransaction();
            try {
                Log::info("Досрочно победили атакующие. Осталось в живых = $xa");

                $loots = $this->rob(); // начать грабить.
                $this->comeback(); // вернуться назад в замок.

                // Запуск события, что отряд либо победил, либо был разгроблен.
                event(new SquadAssaulted($this, ['status' => 'win', 'loots' => $loots]));
            } catch (\Exception $ex) {
                DB::rollBack();
                throw($ex); // next...
            }
            DB::commit();
            return;
        }

        // Рассчет мощностей каждой из армий и их разницы...
        $rand = rand(0-$xd / 5, $xa / 5); // рандом при расчете мощностей
        $diff = intval($xa * $ya - $xd * $yd * (1 + $zd / 100) + $rand);

        Log::info("Мощность атакующих = " . ($xa * $ya) . ". Мощность защитников = " . ($xd * $yd * (1 + $zd / 100)));
        Log::info("Первый рандом = $rand. Разница мощностей с рандомом = $diff");

        $rand = rand(0, 150) / 1000; // рандом при вычилсение сил победителей
        Log::info("Второй рандом = $rand");

        // Расчет сил победителей и награды, в случаи победы атакующих...
        DB::beginTransaction();
        try {
            $loots = [];
            if ($diff > 0) {
                // Атакующий отряд победил...
                $left = ($diff / $ya) * (1 + $rand);
                $left = intval($left > $xa ? $xa : $left);
                // Оставить только выживших...
                $this->update(['size' => $left]);
                $dArmy->reset();

                Log::info("Победили атакующие. Осталось в живых = $left");

                $loots = $this->rob(); // начать грабить.
                $this->comeback(); // вернуться назад в замок.
                $status = 'win';
            } elseif ($diff < 0) {
                // Защитники победили...
                $left = ($diff / $yd) * (1 + $zd / 100) * (1 + $rand);
                $left = abs(intval($left > $xd ? $xd : $left));
                // Оставить только выживших...
                $this->delete();
                $dArmy->update(['size' => $left]);

                Log::info("Победили защитники. Осталось в живых = $left");
                $status = 'def';
            } else {
                // Ничья.
                $this->delete();
                $dArmy->reset();

                Log::info("Ничья. Все умерли.");
                $status = 'draw';
            }
            

            // Запуск события, что отряд либо победил, либо был разгроблен.
            event(new SquadAssaulted($this, ['status' => $status, 'loots' => $loots]));
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }

        DB::commit();
    }

    /**
     * Ограбить замок и унести награбленное с собой.
     *
     * @throws GameException
     * @throws \Exception
     */
    public function rob()
    {
        $this->continueOrException();

        // Вражеский замок...
        $goal = $this->goal;

        $now = Carbon::now();
        Log::info('---------------------------------------------------------------------------------------------------');
        Log::info("($now) Грабеж замка id={$goal->id} '{$goal->name}' отрядом id={$this->id} '{$this->name}' ({$this->size} в)...");

        $loots = []; // награбленное
        // Ресурсы, которые может унести отряд...
        $resAvailable = ['wood', 'gold', 'food'];
        // Количество каждого ресурса, которого может унести весь отряд...
        $resOnSquad = 5 * $this->size;
        // Остаток. Если недостаточно забрано ресурсов определенного типа,
        // то попытаться возместить это ресурсами другого типа.
        $residue = 0;

        Log::info("Количетво каждого ресурса, который может унести весь отряд = $resOnSquad");

        DB::beginTransaction();
        try {
            // Извлечь все ресурсы этого замка...
            foreach ($goal->resources()->get() as $res) {
                $exists = $res->pivot->count; // Имеющиеся ресурсы замка.
                // Ресурс который подлежит грабежу?
                if (in_array($res->name, $resAvailable)) {
                    // Расчитать возможность забрать весь ресурс данного типа...
                    $diff = $exists - $resOnSquad - $residue;
                    if ($diff < 0) {
                        // ... иначе расчитать остаток, который будет использован для следующего ресурса...
                        $residue = abs($exists - $resOnSquad - $residue);
                        $loot = $exists;
                    } else {
                        // ...обнулить остаток и присвоить добычу.
                        $residue = 0;
                        $loot = $diff;
                    }
                    // Забрать ресурс из замка и добавить в награды...
                    if ($goal->subResource($res, $loot)) {
                        $loots[] = [$res->name => $loot];
                        $this->resources()->attach($res->id, ['count' => $loot]);

                        Log::info("Имеется $exists ресурса id={$res->id} '{$res->name}'. Изъято отрядом в количестве = {$loot}");
                    }
                }
            }
            Log::info("Грабеж закончен.");
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();

        return $loots;
    }

    /**
     * Расформировать отряд. Положить награбленное в казну замка и отпустить храбрых воинов отряда на покой.
     *
     * @throws \Exception
     */
    public function disband()
    {
        // Армия и замок отряда...
        $army = $this->army;
        $castle = $army->castle;
        $loots = []; // награбленное.

        $now = Carbon::now();
        Log::info('---------------------------------------------------------------------------------------------------');
        Log::info("($now) Расформирование отряда id={$this->id} '{$this->name}' ({$this->size} в) замка id={$castle->id} '{$castle->name}'...");

        DB::beginTransaction();
        try {
            // Добавить все награбленное отрядом в замок...
            foreach ($this->resources()->getResults() as $res) {
                $loot = $res->pivot->count; // Имеющиеся ресурс из награбленного отрядом...
                $castle->addResource($res, $loot); // ...теперь в замке

                $loots[] = [$res->name => $loot]; // награбленное
                Log::info("Добавлено $loot ресурса id={$res->id} '{$res->name}' в замок");
            }
            $beforeSize = $army->size;

            // Удалить все награбленное из отряда...
            $this->rewards()->delete();
            // Вернуть людей в замок...
            $army->size += $this->size;
            // Расформировать храбрый отряд и обновить армию...
            $this->delete();
            $army->save();

            Log::info("Воинов в замке было $beforeSize. После дизбанда воинов стало = {$army->size}");
            Log::info("Расформирование закончено.");
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();
        // Запуск события, что отряд вернулся домой и был расформирован.
        event(new SquadDisbanded($this, ['loots' => $loots]));

        return $loots;
    }

    /**
     * Вернуться обратно в свой замок если это возможно.
     *
     * @throws GameException
     */
    public function comeback()
    {
        $this->continueOrException();

        $goal = $this->goal;
        $castle = $this->army->castle;

        // Поход уже закончился или еще не начался...
        if (isset($this->crusade_end_at) || !(isset($this->crusade_at) && isset($this->battle_at))) {
            throw new GameException('Отряд уже закончил поход или похода не существует.');
        }

        // Рассчитать время возвращения домой отряда...
        $minutes = Location::howMuchTime($castle, $goal);
        $minutes = intval($minutes * 1.15); // С учетом усталости отряда...
        $end = $this->crusade_end_at = $this->battle_at->addMinutes($minutes); // дата возвращения отряда.

        $this->save();

        $now = Carbon::now();
        Log::info('---------------------------------------------------------------------------------------------------');
        Log::info("($now) Отряд id={$this->id} '{$this->name}' вернется в замок {$this->crusade_end_at}.");

        return $end;
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
     * Получить награбленные ресурсы или ресурс отряда.
     *
     * @param null $resource
     * @return \Illuminate\Support\Collection|int|null
     */
    public function getRewards($resource = null)
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

    public function __get($key)
    {
        // Получить состояние отряда...
        if ($key == 'state') {
            $now = Carbon::now();
            $end = $this->crusade_end_at; // конец похода
            $start = $this->crusade_at;  // начало похода
            $battle = $this->battle_at; // время битвы
            $crusade = isset($start) && isset($battle) && $start->lt($battle);// поход существует?

            if ($crusade && isset($end) && $now->lt($end)) {
                $state = 'comeback';
            } elseif ($crusade && !isset($end) && $now->gte($battle)) {
                $state = 'assault';
            } elseif ($crusade && !isset($end) && $now->lt($battle) && $now->gte($start)) {
                $state = 'crusade';
            } else {
                $state = 'idle';
            }
            return $state;
        }

        // Получить подробное человеческое описание состояния отряда...
        if ($key == 'hstate') {
            $state = $this->state;
            switch ($state) {
                case 'comeback':
                    $hstate = "Вернется из похода {$this->crusade_end_at->format('d.m.Y H:i')}"; break;
                case 'assault':
                    $hstate = 'Штурмует вражеский замок'; break;
                case 'crusade':
                    $hstate = "В походе на {$this->goal->name}. Сражение {$this->battle_at->format('d.m.Y H:i')}"; break;
                default:
                    $hstate = 'Бездействует';

            }
            return $hstate;
        }

        // Получить пользователя.
        if ($key == 'user') {
            return $this->army ? ($this->army->castle ? $this->army->castle->user : null) : null;
        }

        return parent::__get($key);
    }
}