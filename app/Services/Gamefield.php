<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 21:38
 */

namespace App\Services;

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
     * A Class of model in which are stored the location.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $Model;

    public function __construct()
    {
        $this->height = Config::get('services.locator.height', 2);
        $this->width  = Config::get('services.locator.width', 2);

        $this->Model = Config::get('services.locator.model');
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
        $count = $this->width * $this->height * 10;
        for($i = 0; $i < $count; ++$i) {
            $location = $this->randomLocation();
            if (!$this->hasLocation($location)) {
                return $location;
            }
        }

        return false;
    }

    /**
     * Check the existence of location on the gamefield.
     *
     * @param array $location
     * @return bool
     */
    public function hasLocation(array $location)
    {
        $sl = json_encode($location);
        $instance = $this->Model;
        return ! is_null($instance::whereLocation($sl)->get());
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