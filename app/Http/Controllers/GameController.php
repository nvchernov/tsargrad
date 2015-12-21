<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 21.12.2015
 * Time: 0:25
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class GameController
 * @package App\Http\Controllers
 */
class GameController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('game.army');
        $this->middleware('ajax', ['only' => ['armyCrusade', 'armyBuy', 'armyUpgrade']]);
    }

    /**
     * 'game/' - главная страница игры, игровая карта.
     */
    public function index()
    {

    }

    /**
     * 'game/army/crusade - POST AJAX запрос на создание нового отряда для похода.
     * @param Request $request
     */
    public function armyCrusade(Request $request)
    {

    }

    /**
     * 'game/army/buy' - POST AJAX запрос для покупки воинов в армию.
     * @param Request $request
     */
    public function armyBuy(Request $request)
    {

    }

    /**
     * 'game/army/upgrade' - POST AJAX запрос для улучшения армии.
     * @param Request $request
     */
    public function armyUpgrade(Request $request)
    {

    }
}