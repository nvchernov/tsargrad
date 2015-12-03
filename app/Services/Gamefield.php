<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 21:38
 */

namespace App\Services;

use Config;
use App\Exceptions\GameExecption;

/**
 * Gamefield of the tsargrad game.
 *
 * Class GameField
 * @package App\Services
 */
class GameField
{
    /**
     * A max height of the gamefield.
     *
     * @var int
     */
    protected $height = 0;

    /**
     * A max width of the gamefield.
     *
     * @var int
     */
    protected $width = 0;

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
    protected $model;

    /**
     * Extract a location from model, json or array.
     *
     * @param $loc
     * @return array
     */
    public function extractLocation($loc)
    {
        if ($loc instanceof \Illuminate\Database\Eloquent\Model) {
            $loc = $loc->location;
        }

        if (is_array($loc)) {
            if (!array_key_exists('x', $loc) || !array_key_exists('y', $loc)) {
                return false;
            }
            return ['x' => $loc['x'], 'y' => $loc['y']];
        }

        if (is_string($loc)) {
            $loc = json_decode($loc);
        }

        if (is_object($loc)) {
            if (!property_exists($loc, 'x') || !property_exists($loc, 'y')) {
                return false;
            }
            return ['x' => $loc->x, 'y' => $loc->y];
        }

        return false;
    }

    public function __construct()
    {
        $options = Config::get('services.gamefield');
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
     * Add a object to gamefield if he not already exists.
     *
     * @param $model
     * @return mixed
     * @throws GameExecption
     */
    public function addIfNotExist($model)
    {
        if ($model instanceof $this->model) {

            if (!isset($model->location)) {
                $loc = $this->uniqueLocation();

                if ($loc === false) {
                    throw new GameExecption('You cannot add a castle. All the playing field is occupied.');
                }

                $model->location = $loc;
            }
        }

        return $model;
    }

    /**
     * Get all locations in the gamefield.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function allLocations()
    {
        $arr = [];
        for ($x = 0; $x < $this->width; $x++) {

            for ($y = 0; $y < $this->height; $y++) {
                //array_push($arr, ['x' => $x, 'y' => $y]);
                $arr[] = "{\"x\":$x, \"y\":$y}";
            }
        }

        return collect($arr);
    }

    /**
     * Get random location on the gamefield.
     *
     * @return string - json {x, y}
     */
    public function randomLocation()
    {
        return $this->allLocations()->random();
    }

    /**
     * Get busy locations of the gamefield.
     *
     * @return \Illuminate\Support\Collection
     */
    public function busyLocations()
    {
        $instance = $this->model;
        $collection = $instance::all(['location'])->pluck('location');

        $gfield = $this;
        $arr = [];
        $collection->each(function ($val) use ($gfield, &$arr) {
            $loc = $gfield->extractLocation($val);
            if ($loc == false) { return; }

            for ($i = max($loc['x'] - $gfield->bounds, 0); $i <= min($loc['x'] + $gfield->bounds, $gfield->width); $i++) {
                for ($j = max($loc['y'] - $gfield->bounds, 0); $j <= min($loc['y'] + $gfield->bounds, $gfield->height); $j++) {
                    $l = "{\"x\":$i, \"y\":$j}";
                    if (!in_array($l, $arr)) { $arr[] = $l; }
                }
            }
        });

        return collect($arr);
    }

    /**
     * Get unique location on the gamefield.
     *
     * @return array|false - json {x, y}
     */
    public function uniqueLocation()
    {
        $free = $this->allLocations()->diff($this->busyLocations());
        return count($free) > 0 ? $free->random() : false;
    }

    /**
     * Check the existence of location on the gamefield.
     *
     * @param $location
     * @return bool
     */
    public function hasBusy($location)
    {
        $loc = self::extractLocation($location);

        $instance = $this->model;
        $query = $instance::query();
        // Check a location on bounds of a gamefield area.
        for ($i = max($loc['x'] - $this->bounds, 0); $i <= min($loc['x'] + $this->bounds, $this->width); $i++) {
            for ($j = max($loc['y'] - $this->bounds, 0); $j <= min($loc['y'] + $this->bounds, $this->height); $j++) {
                $query->orWhere('location', '=', "{\"x\":$i, \"y\":$j}");
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
     */
    public function distance($a, $b)
    {
        $a = $this->extractLocation($a);
        $b = $this->extractLocation($b);

        return sqrt(pow($a['x'] - $b['x'], 2) + pow($a['y'] - $b['y'], 2));
    }

    /**
     * How much time has passed for a given distance.
     *
     * @return int minutes
     */
    public function howMuchTime()
    {
        $distance = func_get_arg(0);
        if (func_num_args() >= 2) {
            $distance = $this->distance(func_get_arg(0), func_get_arg(1));
        }
        return intval(5.0 * ($distance + $this->speed - 1.0));
    }
}