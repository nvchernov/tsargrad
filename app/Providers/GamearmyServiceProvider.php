<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 30.11.2015
 * Time: 19:37
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GameArmy;


class GameArmyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the services provider.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('gamearmy', function()
        {
            return new GameArmy;
        });
    }
}