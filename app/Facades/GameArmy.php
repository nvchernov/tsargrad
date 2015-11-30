<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 15.11.2015
 * Time: 23:44
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GameArmy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gamearmy';
    }
}