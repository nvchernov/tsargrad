<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 16:03
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Castle;
use Faker\Factory;

class CastlesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create();

        foreach(range(1, 10) as $i) {
            Castle::create(['name' => $faker->colorName]);
        }
    }
}