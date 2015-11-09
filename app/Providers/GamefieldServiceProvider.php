<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 10.11.2015
 * Time: 0:07
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Gamefield;

class GamefieldServiceProvider extends ServiceProvider
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
        $this->app->bind('gamefield', function()
        {
            return new Gamefield;
        });
    }
}