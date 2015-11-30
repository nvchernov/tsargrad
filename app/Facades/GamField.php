<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 28.11.2015
 * Time: 10:00
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GameField extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gamefield';
    }
}