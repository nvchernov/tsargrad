<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 10.11.2015
 * Time: 23:10
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Castle;
use App\Observers\CastleObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the services provider.
     *
     * @return void
     */
    public function boot()
    {
        Castle::observe(new CastleObserver);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }
}