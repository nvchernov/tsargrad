<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 09.11.2015
 * Time: 16:03
 */

namespace Database\Seeders;

use App\Models\Castle, App\Models\Resource, App\Facades\GameField;
use Illuminate\Database\Seeder;
use Log;

class CastlesTableSeeder extends Seeder
{
    public function run()
    {
        // Base...
        $m = GameField::addIfNotExist(Castle::firstOrNew(['name' => 'Moscow'])); $m->save();
        $v = GameField::addIfNotExist(Castle::firstOrNew(['name' => 'Volgograd'])); $v->save();

        // Resources...
        $gold = Resource::firstOrCreate(['name' => 'gold']);
        $iron = Resource::firstOrCreate(['name' => 'iron']);
        $food = Resource::firstOrCreate(['name' => 'food']);

        // Score...
        //$m->scores()->create(['resource_id' => $gold->id, 'count' => 150]);
        //$m->scores()->create(['resource_id' => $iron->id, 'count' => 250]);
        //$v->scores()->create(['resource_id' => $gold->id, 'count' => 150]);
    }
}