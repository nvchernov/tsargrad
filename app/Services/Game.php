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
use Carbon\Carbon;
use DB;
use Config;

class Game
{
    protected $gamefield;

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
        $this->gamefield = new Gamefield();

        $options = Config::get('services.game');
        unset($options['gamefield']);

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
     * Create a new castle.
     *
     * @param $name
     * @return Castle
     * @throws \Exception
     */
    public function newCastle($name)
    {
        DB::beginTransaction();

        $castle = new Castle(['name' => $name]);
        $army = new Army(['name' => "$name's army", 'size' => $this->baseArmySize, 'level' => $this->baseArmyLevel ]);

        try {
            $this->addToGamefield($castle);
            $castle->save();
            $castle->army()->save($army);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw($ex); // next...
        }

        DB::commit();

        return $castle;
    }

    /**
     * Add a castle to gamefield.
     *
     * @param Castle $c
     * @return Castle
     * @throws GameExecption
     */
    public function addToGamefield(Castle $c)
    {
        if (!isset($c->location)) {
            $loc = $this->gamefield->uniqueLocation();
            if ($loc === false) {
                throw new GameExecption('You cannot add a castle. All the playing field is occupied.');
            }
            $c->location = $loc;
        }

        return $c;
    }

    /**
     * Defence a castle!
     *
     * @param Castle $defended
     * @param Squad $attacker
     * @return array
     * @throws GameExecption
     */
    public function defenceCastle(Castle $defended, Squad $attacker)
    {
        return [];
    }

    /**
     * Create a new squad.
     *
     * @param Castle $home
     * @param string $name
     * @param int $count
     * @param Castle $goal
     * @return Squad
     * @throws \Exception
     */
    public function newSquad(Castle $home, $name, $count, Castle $goal)
    {
        $army = $home->army;

        $diff = $army->size - $count;
        if ($diff < 0) {
            throw new GameExecption('You cannot create a new squad. It\'s not enough army.');
        }

        $squad = new Squad(['name' => $name, 'size' => $diff]);
        $squad->crusade_at = Carbon::now(); // Crusade begin...
        $minutes = $this->gamefield->howMuchTime($home, $goal); // Battle will happen on time ...
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
     * Disband squad and return a reward.
     *
     * @param Squad $s
     * @return array
     */
    public function disbandSquad(Squad $s)
    {
        return [];
    }

    /**
     * Buy a new army.
     *
     * @param Army $a
     * @param $count
     */
    public function buyToArmy(Army $a, $count)
    {

    }

    /**
     * Upgrade an army.
     *
     * @param $Army
     * @param int $toLevel
     */
    public function upgradeArmy($Army, $toLevel = 1)
    {

    }
}