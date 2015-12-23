<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 22.12.2015
 * Time: 23:23
 */

namespace App\Models;

use App\Exceptions\GameException;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    protected $fillable = ['x', 'y', 'castle_id'];

    // $x, $y
    // 'x, y'
    // [x => val, y => val]
    // model
    public static function extract()
    {
        $instance = null;

        $num = func_num_args();
        if ($num > 1) {
            $x = func_get_arg(0);
            $y = func_get_arg(1);
        } elseif ($num == 1) {
            $arg = func_get_arg(0);

            if ($arg instanceof static && $arg->exists) {
                $instance = $arg;
            } elseif ($arg instanceof Castle && $arg->location) {
                $instance = $arg->location;
            } elseif (is_string($arg) && count(explode(',', $arg)) == 2) {
                $arg = explode(',', $arg);
            }

            if (is_array($arg) && count($arg) >= 2) {
                if (array_keys($arg) !== range(0, count($arg) - 1)) {
                    $x = $arg['x'];
                    $y = $arg['y'];
                } else {
                    $x = $arg[0];
                    $y = $arg[1];
                }
            }
        }

        if (isset($x) && isset($y)) {
            $instance = static::where(['x' => $x])->where(['y' => $y])->first();
        }

        return $instance;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function free()
    {
        return static::whereNull('castle_id')->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function busy()
    {
        return static::whereNotNull('castle_id')->get();
    }

    /**
     * @return mixed|null
     */
    public static function freeRandom()
    {
        $free = static::free();
        return $free->count() == 0 ? null : $free->random();
    }

    public static function hasBusy()
    {
        $inst = static::extract(func_get_args());
        return $inst ? !empty($inst->castle_id) : null;
    }

    public static function distance($a, $b)
    {
        $a = static::extract($a);
        $b = static::extract($b);

        return $a && $b ? sqrt(pow($a->x - $b->x, 2) + pow($a->y - $b->y, 2)) : null;
    }

    public static function howMuchTime($a, $b)
    {
        $dist = static::distance($a, $b);
        return $dist ? intval(5.0 * $dist) : null;
    }

    public function castle()
    {
        return $this->belongsTo('App\Models\Castle');
    }
}