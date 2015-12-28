<?php

use Illuminate\Database\Seeder;

class CastleBuildingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Лесопилка - лес
        DB::table('buildings')->insert([
            'building_name' => 'sawmill',
            'resources_id' => 2
        ]);
        
        // Шахта - золото
        DB::table('buildings')->insert([
            'building_name' => 'mine', 
            'resources_id' => 1
        ]);
        
        // Ферма - еда
        DB::table('buildings')->insert([
            'building_name' => 'farm',
            'resources_id' => 3
        ]);
        
        // Защитные сооружения - фортификация
        DB::table('buildings')->insert([
            'building_name' => 'defenses', 
        ]);
    }
}
