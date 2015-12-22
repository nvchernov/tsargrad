<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Database\Seeders\CastlesTableSeeder,
    Database\Seeders\ArmiesTableSeeder,
    Database\Seeders\SquadsTableSeeder,
    Database\Seeders\ResourcesTableSeeder,
    Database\Seeders\AdministratorsTableSeeder,
    Database\Seeders\NewsTableSeeder,
    Database\Seeders\LocationsTableSeeder;

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

        $this->call(CommentBlocksTableSeeder::class);
        $this->call(ResourcesTableSeeder::class);
        $this->call(CastlesTableSeeder::class);
        $this->call(ArmiesTableSeeder::class);
        $this->call(SquadsTableSeeder::class);
        $this->call(AdministratorsTableSeeder::class);
        $this->call(NewsTableSeeder::class);
        $this->call(LocationsTableSeeder::class);

        Model::reguard();
    }
}
