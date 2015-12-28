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
        ]);
        
        // Шахта - золото
        DB::table('buildings')->insert([
            'building_name' => 'mine', 
        ]);
        
        // Ферма - еда
        DB::table('buildings')->insert([
            'building_name' => 'farm', 
        ]);
        
        // Защитные сооружения - фортификация
        DB::table('buildings')->insert([
            'building_name' => 'defenses', 
        ]);
    }
}
