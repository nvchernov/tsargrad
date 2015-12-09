<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 15.11.2015
 * Time: 23:38
 */

namespace App\Services;

use App\Models\Army, App\Models\Castle, App\Models\Squad;
use App\Exceptions\GameException;
use Carbon\Carbon, DB, Config;
use App\Facades\GameField;

class GameArmy
{
    /**
     * A default level of army.
     *
     * @var int
     */
    private $baseArmyLevel = 1;

    /**
     * A default size of army.
     *
     * @var int
     */
    private $baseArmySize = 10;

    /**
     * Усталость армии. Влияет на скорость возвращения домой.
     *
     * @var int
     */
    private $fatigueArmy = 1;

    /**
     * Еденицы ресурса на воина при грабеже.
     * @var array
     */
    private $resourcesPerRob = [];

    public function __construct()
    {
        $options = Config::get('services.gamearmy');
        foreach ($options as $k => $v) {
            if (property_exists(static::class, $k)) {
                $this->$k = $v;
            }
        }
    }

    public function __get($key)
    {
        return $this->$key;
    }

    /**
     * Reset army to default.
     *
     * @param Castle $c
     * @return Army
     * @throws GameException
     */
    public function resetArmy(Castle $c)
    {
        if (!$c->exists) {
            throw new GameException('Данный замок еще не сушествует...');
        }

        if (isset($c->army)) {
            $a = $c->army;

            $a->level = $this->baseArmyLevel;
            $a->size = 0; // $this->baseArmySize ?;

            $a->save();
            return $a;
        }

        return $this->addIfNotExistArmy($c);
    }

    /**
     * Add army to castle if the castle exists and no army.
     *
     * @param Castle $c
     * @return Army
     * @throws GameException
     */
    public function addIfNotExistArmy(Castle $c)
    {
        if (!$c->exists) {
            throw new GameException('Данный замок еще не сушествует...');
        }

        $army = $c->army;
        if (!isset($army)) {
            $army = new Army([
                'name' => "{$c->name}'s army",
                'size' => $this->baseArmySize,
                'level' => $this->baseArmyLevel
            ]);
            $c->army()->save($army);
        }

        return $army;
    }

    /**
     * Battle!!!
     *
     * @param Castle $defender
     * @param Squad $attacker
     * @return array
     * @throws GameException
     */
    public function battle(Squad $attacker, Castle $defender)
    {
        return [];
    }

    /**
     * Тысяча чертей!!! Отправить в новый поход на вражеский замок отряд храбрых воинов.
     *
     * @param Castle $home родной замок
     * @param string $name имя храброго отряда
     * @param int $count количество храбрых воинов
     * @param Castle $goal вражеский замок
     * @return Squad храбрый отряд.
     * @throws \Exception
     */
    public function crusade(Castle $home, $name, $count, Castle $goal)
    {
        $army = $this->addIfNotExistArmy($home);

        $diff = $army->size - $count;
        if ($diff < 0) {
            throw new GameException('Нельзя создать отряд для похода. Не хватает храбрых воинов.');
        }

        $squad = new Squad(['name' => $name, 'size' => $count]);
        $squad->crusade_at = Carbon::now(); // Crusade begin...
        $minutes = GameField::howMuchTime($home, $goal); // Battle will happen on time ...
        $squad->battle_at = Carbon::now()->addMinutes($minutes);

        DB::beginTransaction();
        try {
            $squad->crusadeable()->associate($goal);
            $army->squads()->save($squad);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();

        return $squad;
    }

    /**
     * Отправится отряду в путь обратно домой.
     *
     * @param Squad $s
     * @return bool
     * @throws GameException
     */
    public function recrusade(Squad $s)
    {
        if (!$s->exists) {
            throw new GameException('Данного отряда не сушествует...');
        }

        // Поход уже закончился или еще не начался...
        if (isset($s->crusade_end_at) || !(isset($s->crusade_at) && isset($s->battle_at))) {
            return false;
        }

        // Рассчитать время возвращения домой отряда...
        $minutes = GameField::howMuchTime($s->army->castle, $s->goal);
        $s->crusade_end_at = Carbon::now()->addMinutes($minutes * $this->fatigueArmy);

        return $s->save();
    }

    /**
     * ГРААБЕЖЖ! Ограбить отрядом замок.
     *
     * @param Squad $robber грабитель
     * @param Castle $goal жертва
     * @return bool успешность ограбления
     * @throws \Exception
     */
    public function rob(Squad $robber, Castle $goal)
    {
        if (!$robber->exists) {
            throw new GameException('Данного отряда не сушествует...');
        }

        // Размер отряда...
        $squadSize = $robber->size;
        if ($squadSize == 0) {
            return false;
        }

        DB::beginTransaction();
        try {
            // Извлечь все ресурсы этого замка...
            foreach ($goal->resources()->get() as $res) {
                $exists = $res->pivot->count; // Имеющиеся ресурсы замка.
                // Ресурс который подлежит грабежу?
                if (array_key_exists($res->name, $this->resourcesPerRob)) {
                    // Количество ресурсов, которые могут унести весь отряд...
                    $size = $this->resourcesPerRob[$res->name] * $squadSize;
                    $loot = ($exists - $size) < 0 ? $exists : $exists - $size; // Добыча

                    // Забрать ресурс из замка и добавить в награды...
                    if ($goal->subResource($res, $loot)) {
                        $robber->resources()->attach($res->id, ['count' => $loot]);
                    }
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();

        return true;
    }

    /**
     * Пиршерство! Положить награбленное в казну замка и отпустить храбрых воинов отряда пировать.
     *
     * @param Squad $s храбрый отряд воинов
     * @return bool
     * @throws \Exception
     */
    public function feast(Squad $s)
    {
        if (!$s->exists) {
            throw new GameException('Данного отряда не сушествует...');
        }

        // Армия и замок отряда...
        $army = $s->army;
        $home = $army->castle;

        DB::beginTransaction();
        try {
            // Добавить все награбленное отрядом в замок...
            foreach ($s->resources()->get() as $res) {
                $count = $res->pivot->count; // Имеющиеся ресурс из награбленного отрядом...
                $home->addResource($res, $count); // ...теперь в замке
            }
            // Удалить все награбленное из отряда...
            $s->rewards()->delete();
            // Вернуть людей в замок...
            $army->size += $s->size;
            // Расформировать храбрый отряд...
            $s->delete();

            $result = $army->save();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();

        return $result;
    }

    /**
     * Формула расчета стоимости покупки воинов в армию...
     *
     * @param int $level уровень
     * @param int $count количество воинов для покупки
     * @return int
     */
    protected static function formulaBuyArmy($level, $count)
    {
        return $level * 2 * $count;
    }

    /**
     * Купить новых воинов в армию замка.
     *
     * @param Castle $c замок.
     * @param int $count количество солдат для покупки.
     * @return bool
     * @throws \Exception
     */
    public function buyArmy(Castle $c, $count)
    {
        if (!(is_integer($count) && $count > 0)) {
            return false;
        }
        $army = $this->addIfNotExistArmy($c);

        // Стоимость покупки пехотинцев...
        $cost = static::formulaBuyArmy($army->level, $count);
        // Количество еды и железа в замке...
        $iron = $c->getResources('iron');
        $food = $c->getResources('food');

        // Хватает ресурсов?
        if ($iron < $cost || $food < $cost) {
            $mi = "ЖЕЛЕЗА ({$iron} / {$cost})";
            $mf = "ЕДЫ ({$food} / {$cost})";
            $des = $iron < $cost && $food < $cost ? $mi . ' и ' . $mf : $iron < $cost ? $mi : $mf;
            throw new GameException("Нельзя купить новых воинов. Не хватает $des");
        }

        // Собственно покупка...
        DB::beginTransaction();
        try {
            $c->subResource('iron', $cost);
            $c->subResource('food', $cost);
            $army->size += $count;
            $army->save();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();

        return true;
    }

    /**
     * Формула расчета стоимости апгрейда армии...
     *
     * @param int $level уровень
     * @param int $strength количество воинов в армии на данный момент
     * @return int
     */
    protected static function formulaUpgradeArmy($level, $strength)
    {
        return intval(log($level + 1, 2) * 8 * ($strength + 1));
    }

    /**
     * Улучшить уровень армии замка.
     *
     * @param Castle $c
     * @param int $toLevel на сколько увеличить уровней сразу?
     * @return bool
     * @throws \Exception
     */
    public function upgradeArmy(Castle $c, $toLevel = 1)
    {
        if (!(is_integer($toLevel) && $toLevel > 1)) {
            return false;
        }
        $army = $this->addIfNotExistArmy($c);

        // Стоимость апргрейда на указанное число уровней...
        $cost = static::formulaUpgradeArmy($army->level, $army->strength);
        $level = $army->level + 1; // следующий уровень...
        while ($level < $army->level + $toLevel) {
            $cost += static::formulaUpgradeArmy($level++, $army->strength);
        }

        // Количество золота в замке...
        $gold = $c->getResources('gold');
        // Хватает ресурсов?
        if ($gold < $cost) {
            throw new GameException("Нельзя улучшить армию. Не хватает ЗОЛОТА ($gold / $cost)");
        }

        // Собственно покупка...
        DB::beginTransaction();
        try {
            $c->subResource('gold', $cost);
            $army->level += $toLevel;
            $army->save();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }
        DB::commit();

        return true;
    }
}