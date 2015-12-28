<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 09.11.2015
 * Time: 16:17
 */

namespace Database\Seeders;

use App\Models\Amulet;
use App\Models\Hair;
use App\Models\Mustache;
use App\Models\Flag;
use Illuminate\Database\Seeder;

class AvatarDefaultSeeder extends Seeder
{
    public function run()
    {
//        Amulet::truncate();

        Amulet::create(['image_url' => '\\images\\default_avatar\\bottom\\bottom_01.png']);
        Amulet::create(['image_url' => '\\images\\default_avatar\\bottom\\bottom_02.png']);
        Amulet::create(['image_url' => '\\images\\default_avatar\\bottom\\bottom_03.png']);
        Amulet::create(['image_url' => '\\images\\default_avatar\\bottom\\bottom_04.png']);
        Amulet::create(['image_url' => '\\images\\default_avatar\\bottom\\bottom_05.png']);

        Hair::create(['image_url' => '\\images\\default_avatar\\top\\top_01.png']);
        Hair::create(['image_url' => '\\images\\default_avatar\\top\\top_02.png']);
        Hair::create(['image_url' => '\\images\\default_avatar\\top\\top_03.png']);
        Hair::create(['image_url' => '\\images\\default_avatar\\top\\top_04.png']);
        Hair::create(['image_url' => '\\images\\default_avatar\\top\\top_05.png']);

        Mustache::create(['image_url' => '\\images\\default_avatar\\middle\\middle_01.png']);
        Mustache::create(['image_url' => '\\images\\default_avatar\\middle\\middle_02.png']);
        Mustache::create(['image_url' => '\\images\\default_avatar\\middle\\middle_03.png']);
        Mustache::create(['image_url' => '\\images\\default_avatar\\middle\\middle_04.png']);
        Mustache::create(['image_url' => '\\images\\default_avatar\\middle\\middle_05.png']);

        Flag::create(['image_url' => '\\images\\default_avatar\\background\\bg_01.png']);
        Flag::create(['image_url' => '\\images\\default_avatar\\background\\bg_02.png']);
        Flag::create(['image_url' => '\\images\\default_avatar\\background\\bg_03.png']);
        Flag::create(['image_url' => '\\images\\default_avatar\\background\\bg_04.png']);
        Flag::create(['image_url' => '\\images\\default_avatar\\background\\bg_05.png']);
    }
}