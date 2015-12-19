<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 09.11.2015
 * Time: 16:25
 */

namespace App\Models;

use App\Exceptions\GameException;
use App\Facades\GameField;
use Carbon\Carbon;
use DB;
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
     * Проверить на возможность продолжение или выбросить исключение
     * @throws GameException
     */
    private function continueOrException()
    {
        if (!isset($this->army)) {
            throw new GameException('Отряд не может существовать без армии.');
        }
        if (!isset($this->goal)) {
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

        if ($this->state !== 'assault') {
            throw new GameException('Отряд: невозможно штурмовать.');
        }

        // Расчет победителя сражения...
        $aArmy = $this->army; // атакующая армия
        $dArmy = $this->goal->armyOrCreate(); // защищающиеся армия
        // xa, ya - кол-во и уровень атакующих.
        // xd, yd, zd - кол-во, уровень войск и уровень фортификации защищающихся.
        $xa = $this->size;
        $ya = $aArmy->level;
        $xd = $dArmy->size;
        $yd = $dArmy->level;
        $zd = 0; // Уровень фортификации, пока остается 0;

        // Рассчет мощностей каждой из армий и их разницы...
        $rand = rand(0-$xd / 2, $xa / 2); // рандом при расчете мощностей
        $diff = intval($xa * $ya - $xd * $yd * (1 + $zd / 100) + $rand);

        $rand = rand(0, 200) / 1000; // рандом при вычилсение сил победителей
        // Расчет сил победителей и награды, в случаи победы атакующих...
        DB::beginTransaction();
        try {
            if ($diff > 0) {
                // Атакующий отряд победил...
                $left = ($diff / $ya) * (1 + $rand);
                $left = intval($left);
                // Оставить только выживших...
                $this->update(['size' => $left]);
                $dArmy->reset();

                $this->rob(); // начать грабить.
                $this->comeback(); // вернуться назад в замок.
            } elseif ($diff < 0) {
                // Защитники победили...
                $left = ($diff / $yd) * (1 - $zd / 100) * (1 + $rand);
                $left = abs(intval($left));
                // Оставить только выживших...
                $this->delete();
                $dArmy->update(['size' => $left]);
            } else {
                // Ничья.
                // Удалить атакующий отряд и сбросить армию защитников...
                $this->delete();
                $dArmy->reset();
            }
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

        if ($this->state !== 'assault') {
            throw new GameException('Отряд: невозможно ограбить.');
        }

        // Вражеский замок...
        $goal = $this->goal;

        // Ресурсы, которые может унести отряд...
        $resAvailable = ['wood', 'gold', 'food'];
        // Количество каждого ресурса, которого может унести весь отряд...
        $resOnSquad = 5 * $this->size;
        // Остаток. Если недостаточно забрано ресурсов определенного типа,
        // то попытаться возместить это ресурсами другого типа.
        $residue = 0;

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
                        $this->resources()->attach($res->id, ['count' => $loot]);
                    }
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();
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

        DB::beginTransaction();
        try {
            // Добавить все награбленное отрядом в замок...
            foreach ($this->resources()->get() as $res) {
                $count = $res->pivot->count; // Имеющиеся ресурс из награбленного отрядом...
                $castle->addResource($res, $count); // ...теперь в замке
            }
            // Удалить все награбленное из отряда...
            $this->rewards()->delete();
            // Вернуть людей в замок...
            $army->size += $this->size;
            // Расформировать храбрый отряд...
            $this->delete();

            $army->save();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();
    }

    /**
     * Вернуться обратно в свой замок если это возможно.
     *
     * @throws GameException
     */
    public function comeback()
    {
        $this->continueOrException();

        if ($this->state !== 'assault') {
            throw new GameException('Отряд: невозможно вернуться домой.');
        }

        $goal = $this->goal;
        $castle = $this->army->castle;

        // Поход уже закончился или еще не начался...
        if (isset($this->crusade_end_at) || !(isset($this->crusade_at) && isset($this->battle_at))) {
            throw new GameException('Отряд уже закончил поход или похода не существует.');
        }

        // Рассчитать время возвращения домой отряда...
        $minutes = GameField::howMuchTime($castle, $goal);
        $minutes = intval($minutes * 1.2); // С учетом усталости отряда...
        $this->crusade_end_at = Carbon::now()->addMinutes($minutes);

        $this->save();
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
            $now = Carbon::now();
            $end = $this->crusade_end_at; // конец похода
            $start = $this->crusade_at;  // начало похода
            $battle = $this->battle_at; // время битвы
            $crusade = isset($start) && isset($battle) && $start->lt($battle);// поход существует?

            if ($crusade && isset($end) && $now->lte($end)) {
                $state = 'comeback';
            } elseif ($crusade && !isset($end) && $now->gte($battle)) {
                $state = 'assault';
            } elseif ($crusade && !isset($end) && $now->lt($battle) && $now->gte($start)) {
                $state = 'crusade';
            } else {
                $state = 'wait';
            }
            return $state;
        }

        // Получить подробное человеческое описание состояния отряда...
        if ($key == 'hstate') {
            $state = $this->state;
            switch ($state) {
                case 'comeback':
                    $hstate = "В пути домой. Вернется из похода {$this->crusade_end_at->toDateTimeString()}"; break;
                case 'assault':
                    $hstate = 'Штурмует вражеский замок'; break;
                case 'crusade':
                    $hstate = "В походе от {$this->crusade_at->toDateTimeString()}. Штурм состоится {$this->battle_at->toDateTimeString()}"; break;
                default:
                    $hstate = 'Ожидает';

            }
            return $hstate;
        }

        return parent::__get($key);
    }
}