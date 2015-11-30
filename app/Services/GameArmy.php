<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 15.11.2015
 * Time: 23:38
 */

namespace App\Services;

use App\Models\Army, App\Models\Castle, App\Models\Squad;
use App\Exceptions\GameExecption;
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
    private $baseArmySize = 100;


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
     */
    public function resetArmy(Castle $c)
    {
        if ($c->exists && isset($c->army)) {
            $a = $c->army;

            $a->level = $this->baseArmyLevel;
            $a->size = 1; // $this->baseArmySize ?;

            $a->save();
            return $a;
        }

        return $this->addArmy($c);
    }

    /**
     * Add army to castle if the castle exists and no army.
     *
     * @param Castle $c
     * @return Army|false
     */
    public function addArmy(Castle $c)
    {
        $army = false;
        if ($c->exists && !isset($c->army)) {
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
     * @throws GameExecption
     */
    public function battle(Squad $attacker, Castle $defender)
    {
        return [];
    }

    /**
     * WAAAGH-WAAAGH!!! Go crusade on another castle.
     *
     * @param Castle $home
     * @param string $name
     * @param int $count
     * @param Castle $goal
     * @return Squad a created squad for crusade.
     * @throws \Exception
     */
    public function crusade(Castle $home, $name, $count, Castle $goal)
    {
        $army = $home->army;

        $diff = $army->size - $count;
        if ($diff < 0) {
            throw new GameExecption('You cannot create a new squad for crusade. It\'s not enough army.');
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
     * Feast! Disband the squad to army and return a reward.
     *
     * @param Squad $s
     * @return array
     */
    public function feast(Squad $s)
    {
        return [];
    }

    /**
     * Buy a new army.
     *
     * @param Army $a
     * @param $count
     */
    public function buyArmy(Army $a, $count)
    {

    }

    /**
     * Upgrade an army.
     *
     * @param Army $a
     * @param int $toLevel
     */
    public function upgradeArmy(Army $a, $toLevel = 1)
    {

    }
}