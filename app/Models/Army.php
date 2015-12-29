<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 09.11.2015
 * Time: 16:24
 */

namespace App\Models;

use App\Events\CUD;
use App\Exceptions\GameException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Log;

class Army extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'armies';

    protected $fillable = ['name', 'size', 'level'];

    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    /**
     * Формула расчета стоимости покупки воинов в армию.
     *
     * @param int $level уровень
     * @param int $count количество воинов для покупки
     * @return int
     */
    private static function formulaBuy($level, $count)
    {
        return intval(exp($level / 10)) * 5 * $count;
    }

    /**
     * Формула расчета стоимости апгрейда армии...
     *
     * @param int $level уровень
     * @param int $strength количество воинов в армии на данный момент
     * @return int
     */
    private static function formulaUpgrade($level, $strength)
    {
        return intval(exp($level / 10)) * 12 * ($strength + 1);
    }

    public function jsonSerialize()
    {
        $serialized = parent::jsonSerialize();

        return array_merge($serialized, [
            'buyPrice' => $this->buyPrice(), 'upgradePrice' => $this->upgradePrice(),
        ]);
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

    public function defend($level, $count)
    {
        $dArmy = $this; // защищающиеся армия
        $now = Carbon::now();
        // xa, ya - кол-во и уровень атакующих.
        // xd, yd, zd - кол-во, уровень войск и уровень фортификации защищающихся.
        $xa = $count;
        $ya = $level;
        $xd = $this->size;
        $yd = $this->level;
        $zd = 0;//$this->goal->fortification()->level; // Уровень фортификации, пока остается 0;

        Log::info("Сила атак. отряда = $xa и уровень = $ya");
        Log::info("Сила защ. армии = $xd, уровень = $yd и фортифмкация = $zd");

        if ($xd == 0) {
            // Защитников нет, досрочная победа атакующих...
            Log::info("Досрочно победили атакующие. Осталось в живых = $xa");
            return false;
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
                $left = $xd - $left;
                $left = intval($left < 0 ? 0 : $left);
                // Оставить только выживших...
                $dArmy->update(['size' => $left]);
                Log::info("Победили атакующие. Осталось в живых = $left");
                DB::commit();
                return false;
            } elseif ($diff < 0) {
                // Защитники победили...
                $left = ($diff / $yd) * (1 + $zd / 100) * (1 + $rand);
                $left = abs(intval($left > $xd ? $xd : $left));
                // Оставить только выживших...
                $dArmy->update(['size' => $left]);
                Log::info("Победили защитники. Осталось в живых = $left");
                DB::commit();
                return true;
            } else {
                $dArmy->update(['size' => 0]);
                Log::info("Ничья. Все умерли.");
                DB::commit();
                return true;
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
    }

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
     * Цена покупки одного воина.
     * @return int
     */
    public function buyPrice()
    {
        return static::formulaBuy($this->level, 1);
    }

    /**
     * Цена улучшения на 1 уровень армии.
     */
    public function upgradePrice()
    {
        return static::formulaUpgrade($this->level, $this->strength);
    }

    /**
     * Сбросить армию по-умолчанию
     *
     * @return Army
     * @throws \Exception
     */
    public function reset()
    {
        //$this->level = 1; // Уровень не сбрасывать?
        $this->size = 0;

        DB::beginTransaction();
        try {
            // Сохранить армию...
            //$this->squads()->delete(); // отряды не удалять?
            $this->save();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();

        return $this;
    }

    /**
     * Совершить поход на вражеский замок.
     *
     * @param {string} $name имя отряда
     * @param {int} $count количество в отряде
     * @param Castle $goal замок
     * @return Squad
     * @throws GameException
     * @throws \Exception
     */
    public function crusade($name, $count, Castle $goal)
    {
        if ($goal->id === $this->id) {
            throw new GameException('Нельзя отправить отряд на собственный замок.');
        }

        // Есть ли возможность создать отряд?
        $diff = $this->size - $count;
        if ($count <= 0 || $diff < 0) {
            throw new GameException('Нельзя создать отряд для похода. Не хватает храбрых воинов.');
        }

        $squad = new Squad(['name' => $name, 'size' => $count]);
        $squad->crusade_at = Carbon::now(); // Начало похода
        // Время на поход...
        $minutes = Location::howMuchTime($this->castle, $goal);
        $squad->battle_at = Carbon::now()->addMinutes($minutes); // Конец похода

        DB::beginTransaction();
        try {
            $this->update(['size' => $diff]);
            $squad->goal()->associate($goal); // Вражеский замок
            // Сохранить отряд...
            $this->squads()->save($squad);

            $now = Carbon::now();
            //Log::info('---------------------------------------------------------------------------------------------------');
            //Log::info("($now) Создан новый отряд - id={$squad->id} '{$squad->name}' ({$squad->size} в)...");
            //Log::info("Поход на вражеский замок - id={$goal->id} '{$goal->name}");
            //Log::info("Начало похода {$squad->crusade_at}, сражение состоится {$squad->battle_at}");
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();

        return $squad;
    }

    /**
     * Купить новых воинов в армию замка.
     *
     * @param int $count кол-во воинов для покупки
     * @throws GameException
     * @throws \Exception
     */
    public function buy($count)
    {
        if ($count < 1) {
            throw new GameException("Недопустимое количество воинов");
        }

        $castle = $this->castle;
        // Стоимость покупки воинов...
        $cost = static::formulaBuy($this->level, $count);
        // Количество еды и дерева в замке...
        $wood = $castle->getResources('wood');
        $food = $castle->getResources('food');

        // Хватает ресурсов?
        if ($wood < $cost || $food < $cost) {
            $mw = "ДЕРЕВА ($wood / $cost)";
            $mf = "ЕДЫ ($food / $cost)";
            $des = $wood < $cost && $food < $cost ? "$mw и $mf" : ($wood < $cost ? $mw : $mf);
            throw new GameException("Нельзя нанять воинов. Не хватает $des");
        }

        // Собственно покупка...
        DB::beginTransaction();
        try {
            $castle->subResource('wood', $cost);
            $castle->subResource('food', $cost);
            $this->size += $count;
            $this->save();

            $now = Carbon::now();
            //Log::info('---------------------------------------------------------------------------------------------------');
            //Log::info("($now) В армию id={$this->id} '{$this->name}' куплено $count воинов на $cost ДЕРЕВА и ЕДЫ");
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();
    }

    /**
     * Улучшить уровень армии замка.
     *
     * @param int $addLevel на сколько увеличить уровней сразу?
     * @throws GameException
     * @throws \Exception
     */
    public function upgrade($addLevel = 1)
    {
        if ($addLevel < 1) {
            throw new GameException("Недопустимый уровень для улучшения");
        }
        $castle = $this->castle;

        // Стоимость апргрейда на указанное число уровней...
        $cost = static::formulaUpgrade($this->level, $this->strength);
        $level = $this->level + 1; // следующий уровень...
        while ($level < $this->level + $addLevel) {
            $cost += static::formulaUpgrade($level++, $this->strength);
        }

        // Количество золота в замке...
        $gold = $castle->getResources('gold');
        // Хватает ресурсов?
        if ($gold < $cost) {
            throw new GameException("Нельзя улучшить армию. Не хватает ЗОЛОТА ($gold / $cost)");
        }

        // Собственно покупка...
        DB::beginTransaction();
        try {
            $castle->subResource('gold', $cost);
            $this->level += $addLevel;
            $this->save();

            $now = Carbon::now();
            //Log::info('---------------------------------------------------------------------------------------------------');
            //Log::info("($now) Улучшение армии id={$this->id} '{$this->name}' на $addLevel ур. на $cost ЗОЛОТА");
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();
    }

    /**
     * Получить состояния всех отрядов армии.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSquadsStates()
    {
        $arr = [];
        foreach ($this->squads()->getResults() as $s) {
            $arr[$s->id] = $s->hstate;
        }
        return collect($arr);
    }

    /**
     * Получить размер каждого отряда.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSquadsSize()
    {
        $arr = [];
        foreach ($this->squads()->getResults() as $s) {
            $arr[$s->id] = $s->size;
        }
        return collect($arr);
    }

    /**
     * Magic getter!
     *
     * @param string $key
     * @return int|mixed
     */
    public function __get($key)
    {
        // Получить размер всех отрядов армии...
        if ($key == 'sizeOfSquads') {
            $size = 0;
            foreach ($this->squads()->getResults() as $s) {
                $size += $s->size;
            }
            return $size;
        }

        // Получить силу армии...
        if ($key == 'strength') {
            return $this->size + $this->sizeOfSquads;
        }

        // Получить пользователя.
        if ($key == 'user') {
            return $this->castle ? $this->castle->user : null;
        }

        return parent::__get($key);
    }
}