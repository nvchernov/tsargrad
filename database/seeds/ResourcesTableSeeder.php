<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 02.12.2015
 * Time: 12:14
 */

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourcesTableSeeder extends Seeder
{

    public function run()
    {
        // Create necessary resources...
        Resource::firstOrCreate(['name' => 'gold']);
        Resource::firstOrCreate(['name' => 'wood']);
        Resource::firstOrCreate(['name' => 'food']);
    }

}