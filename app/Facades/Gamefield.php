<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 10.11.2015
 * Time: 0:05
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Gamefield extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gamefield';
    }
}