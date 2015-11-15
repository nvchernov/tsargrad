<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 21:38
 */

namespace App\Services;

use App\Exceptions\GameExecption;
use Config;

/**
 * Gamefield of the tsargrad game.
 *
 * Class Gamefield
 * @package App\Services
 */
class Gamefield
{
    /**
     * A max height of the gamefield.
     *
     * @var int
     */
    protected $maxHeight = 0;

    /**
     * A max width of the gamefield.
     *
     * @var int
     */
    protected $maxWidth = 0;

    /**
     * A bounds between castles.
     *
     * @var int
     */
    protected $bounds = 0;

    /**
     * The speed of movement of.
     *
     * @var int
     */
    protected $speed = 0;

    /**
     * A Class of model in which are stored the location.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $Model;

    /**
     * Get a location.
     *
     * @param $l
     * @return array|mixed
     * @throws GameExecption
     */
    protected function getLocation($l)
    {
        if ($l instanceof \Illuminate\Database\Eloquent\Model) {
            return $l->location;
        }

        if (is_array($l)) {
            return ['x' => $l[0], 'y' => $l[1]];
        }

        $obj = json_encode($l);
        if (is_string($l) && $obj == false) {
            throw new GameExecption('Error! No locations.');
        }

        if (is_object($l)) {
            $obj = $l;
        }

        if (!array_key_exists('x', $obj) || !array_key_exists('y', $obj)) {
            throw new GameExecption('Error! No locations.');
        }

        return ['x' => $obj['x'], 'y' => $obj['y']];
    }

    public function __construct()
    {
        $this->maxHeight = Config::get('services.gamefield.height', $this->maxHeight);
        $this->maxWidth = Config::get('services.gamefield.width', $this->maxWidth);
        $this->bounds = Config::get('services.gamefield.bounds', $this->bounds);
        $this->speed = Config::get('services.gamefield.speed', $this->speed);

        $this->Model = Config::get('services.gamefield.model');
    }

    /**
     * Get all locations in the gamefield.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function allLocations()
    {
        $arr = [];
        for ($x = 0; $x < $this->maxWidth; $x++) {

            for ($y = 0; $y < $this->maxHeight; $y++) {
                array_push($arr, ['x' => $x, 'y' => $y]);
            }
        }

        return collect($arr);
    }

    /**
     * Get random location on the gamefield.
     *
     * @return array - {x, y}
     */
    public function randomLocation()
    {
        return $this->allLocations()->random();
    }

    /**
     * Get busy locations of the gamefield.
     *
     * @param int $withBounds bounds
     * @return \Illuminate\Support\Collection
     */
    public function busyLocations($withBounds = 0)
    {
        $width = $this->maxWidth;
        $height = $this->maxHeight;

        $instance = $this->Model;
        $collection = $instance::all(['location'])->pluck('location');

        $busy = collect();
        $collection->each(function ($val) use ($width, $height, $withBounds, &$busy) {
            for ($i = max($val['x'] - $withBounds, 0); $i <= min($val['x'] + $withBounds, $width); $i++) {
                for ($j = max($val['y'] - $withBounds, 0); $j <= min($val['y'] + $withBounds, $height); $j++) {
                    if (!$busy->contains(function ($k, $v) use ($i, $j) {
                        return $v['x'] == $i && $v['y'] == $j;
                    })
                    ) {
                        $busy->push(['x' => $i, 'y' => $j]);
                    }
                }
            }
        });

        return $busy;
    }

    /**
     * Get unique location on the gamefield.
     *
     * @return array|false - {x, y}
     */
    public function uniqueLocation()
    {
        $busy = $this->busyLocations($this->bounds);
        $all = $this->allLocations();

        $free = $all->transform(function ($val) {
            return json_encode($val);
        })
            ->diff($busy->transform(function ($val) {
                return json_encode($val);
            }));

        return count($free) > 0 ? json_decode($free->random()) : false;
    }

    /**
     * Check the existence of location on the gamefield.
     *
     * @param $location
     * @param int $withBounds
     * @return bool
     */
    public function hasBusy($location, $withBounds = 0)
    {
        $l = $this->getLocation($location);

        $instance = $this->Model;
        $query = $instance::query();
        // Check a location on bounds of a gamefield area.
        for ($i = max($l['x'] - $withBounds, 0); $i <= min($l['x'] + $withBounds, $this->maxWidth); $i++) {
            for ($j = max($l['y'] - $withBounds, 0); $j <= min($l['y'] + $withBounds, $this->maxHeight); $j++) {
                $query->orWhere('location', '=', json_encode(['x' => $i, 'y' => $j]));
            }
        }

        return count($query->get()) > 0;
    }

    /**
     * Get the distance between two locations.
     *
     * @param $a
     * @param $b
     * @return float
     * @throws GameExecption
     */
    public function distance($a, $b)
    {
        $l1 = $this->getLocation($a);
        $l2 = $this->getLocation($b);

        return sqrt(pow($l1['x'] - $l2['x'], 2) + pow($l1['y'] - $l2['y'], 2));
    }

    /**
     * How much time has passed for a given distance.
     *
     * @return float|mixed
     */
    public function howMuchTime()
    {
        $distance = func_get_arg(0);
        if (func_num_args() == 2) {
            $distance = $this->distance(func_get_arg(0), func_get_arg(1));
        }
        return $distance * abs(1 - $this->speed);
    }

    /**
     * Get Class of model in which are stored the location.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getLocationModel()
    {
        return $this->Model;
    }

    /**
     * Get a max height of the gamefield.
     *
     * @return int
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * Get a max width of the gamefield.
     * @return int
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }
}