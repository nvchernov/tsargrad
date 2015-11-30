<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 09.11.2015
 * Time: 16:03
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Facades\GameField;
use Log;

class CastlesTableSeeder extends Seeder
{
    public function run()
    {
        $a = '{"x":5, "y":6}';
        $b = '{"x":1, "y":2}';

        $result = GameField::distance($a, $b);
        Log::info("Result = $result");
    }
}