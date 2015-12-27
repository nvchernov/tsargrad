<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\PveEnemy;

class PveEnemiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PveEnemy::create( ['name' => 'Хасбаш Ампутатор'] );
        PveEnemy::create( ['name' => 'Углук Потрошительр'] );
        PveEnemy::create( ['name' => 'Аббас Расчленитель'] );
        PveEnemy::create( ['name' => 'Сабитбек Узурпатор'] );
        PveEnemy::create( ['name' => 'Шабал Линчеватель'] );
    }
}
