<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 23.12.2015
 * Time: 0:53
 */

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    public function run()
    {
        for($i = 0; $i < 20; $i++) {
            for($j = 0; $j < 20; $j++) {
                Location::create(['x' => $i, 'y' => $j]);
            }
        }
    }
}