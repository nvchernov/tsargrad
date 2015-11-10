<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 21:38
 */

namespace App\Services;

use Config;
use Log;

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
    protected $castleBounds = 0;

    /**
     * A Class of model in which are stored the location.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $Model;

    public function __construct()
    {
        $this->height = Config::get('services.gamefield.height', 2);
        $this->width  = Config::get('services.gamefield.width', 2);
        $this->castleBounds = Config::get('services.gamefield.bounds', 0);

        $this->Model = Config::get('services.gamefield.model');
    }

    /**
     * Get random location on the gamefield.
     *
     * @return array
     */
    public function randomLocation()
    {
        return [
            'x' => rand(0, $this->width),
            'y' => rand(0, $this->height)
        ];
    }

    /**
     * Get unique location on the gamefield.
     *
     * @return array|false
     */
    public function uniqueLocation()
    {
        $count = $this->width * $this->height * 10; // why?
        for($i = 0; $i < $count; ++$i) {
            $location = $this->randomLocation();
            if (!$this->hasLocation($location, $this->castleBounds)) {
                return $location;
            }
        }

        return false;
    }

    /**
     * Check the existence of location on the gamefield.
     *
     * @param array $location
     * @param int $withBounds
     * @return bool
     */
    public function hasLocation(array $location, $withBounds = 0)
    {
        $instance = $this->Model;
        $query = $instance::query();
        // Check a location on bounds of a gamefield area.
        for ($i = $location['x'] - $withBounds; $i <= $location['x'] + $withBounds; $i++) {
            for ($j = $location['y'] - $withBounds; $j <= $location['y'] + $withBounds; $j++) {
                $query->orWhere('location', '=', json_encode(['x' => $i, 'y' => $j]));
            }
        }

        return count($query->get()) > 0;
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