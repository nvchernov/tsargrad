<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Database\Seeders\CastlesTableSeeder, Database\Seeders\ArmiesTableSeeder, Database\Seeders\SquadsTableSeeder,
    Database\Seeders\ResourcesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ResourcesTableSeeder::class);
        $this->call(CastlesTableSeeder::class);
        $this->call(ArmiesTableSeeder::class);
        $this->call(SquadsTableSeeder::class);

        Model::reguard();
    }
}
