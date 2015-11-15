<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 10.11.2015
 * Time: 22:20
 */

namespace App\Observers;

use App\Exceptions\GameExecption;
use App\Models\Castle;
use App\Facades\Gamefield;

/**
 * An observer for a castle.
 *
 * Class CastleObserver
 * @package App\Observers
 */
class CastleObserver
{
    public function creating(Castle $model)
    {
        if (!isset($model->location)) {
            $location = Gamefield::uniqueLocation();
            if ($location === false) {
                throw new GameExecption('You cannot create a new castle. All the playing field is occupied.');
            }
            $model->location = $location;
        }
    }

}