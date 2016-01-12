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
        PveEnemy::create( [
            'name' => 'Хасбаш Ампутатор',
            'message' => 'Отродье грязной собаки, я покажу тебе силу орков!'
        ] );
        PveEnemy::create( [
            'name' => 'Углук Потрошитель',
            'message' => 'У меня сегодня день рождения, раскошеливайся дружок.'
        ] );
        PveEnemy::create( [
            'name' => 'Аббас Расчленитель',
            'message' => 'Мерзкие насекомое, сдавайтесь или будете гнить в болоте.'
        ] );
        PveEnemy::create( [
            'name' => 'Сабитбек Узурпатор',
            'message' => 'Мне нужна добыча и твои детишки на завтрок.'
        ] );
        PveEnemy::create( [
            'name' => 'Шабал Линчеватель',
            'message' => 'Слизняк, ты выбрал не то место для своей лачуги.'
        ] );
    }
}
