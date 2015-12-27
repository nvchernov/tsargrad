<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 21.12.2015
 * Time: 0:25
 */

namespace App\Http\Controllers;

use Auth;
use App\Models\Castle;
use App\Models\Army;
use Input;

/**
 * Class GameController
 * @package App\Http\Controllers
 */
class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('game.army');
        $this->middleware('ajax', ['except' => ['getIndex']]);
        $this->middleware('wants.json', ['except' => ['getIndex']]);
    }

    /* Получить все локации...
       $de = 93;
        $x1 = $y1 = 95;
        $x2 = $y2 = $x1 + $de;
            $w = sqrt(Location::count()) * $de + $x1;
            for ($x1 = 95, $y1 = 95; $x1 <= $w && $y1 <= $w; $x1 += $de, $y1 += $de) {
                for ($x2 = $x1 + $de, $y2 = $y1 + $de; $x2 <= $w && $y2 <= $w; $x2 += $de, $y2 += $de) {
                    $data[] = "<area state='$x2-$y2' shape='rect' coords='$x1, $y1, $x2, $y2' href='#'>";
                }
            }
    */

    /**
     * Ajax success response.
     *
     * @param array $data
     * @param int $code
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxResponse(array $data = [], $code = 200, $headers = [])
    {
        return response()->json(['success' => true, 'data' => $data, 'code' => $code], $code, $headers);
    }

    /**
     * Ajax error response.
     *
     * @param $msg
     * @param int $code
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxError($msg = '', $code = 500, $headers = [])
    {
        return response()->json(['success' => false, 'message' => $msg, 'code' => $code], $code, $headers);
    }

    /**
     * game - главная страница игры, игровая карта.
     */
    public function getIndex()
    {
        // данные для представления.
        $data = [];

        $user = $data['user'] = Auth::user();
        $data['castles'] = Castle::has('location')->with('location')->get();
        $c = $data['castle'] = $user->castle;
        $data['resources'] = $c->getResources();

        //require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/game/index.php');
        return view('game/index', $data);
    }

    /**
     * game/castles/{id}
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getCastle($id)
    {
        // данные для представления.
        $data = [];

        $user = Auth::user();
        $data['army'] = $user->army;
        $c = $data['enemy_castle'] = Castle::find($id);
        $data['enemy_resources'] = $c->getResources();

        return $this->ajaxResponse($data);
    }

    /**
     * game/armies/{id}
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getArmy($id)
    {
        // данные для представления.
        $data = [];

        $user = Auth::user();
        $army = $data['army'] = $user->army;
        $data['squads'] = $army->squads;

        return $this->ajaxResponse($data);
    }

    /**
     * game/armies/{id}/crusade - POST AJAX запрос на создание нового отряда для похода.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postArmyCrusade($id)
    {
        if (Input::has('name') && Input::has('goal') && Input::has('count')) {
            try {
                $goal = Castle::find(Input::get('goal'));
                $army = Army::find($id);
                $squad = $army->crusade(Input::get('name'), Input::get('count'), $goal);
                $squad->goal()->getResults();
            } catch (\Exception $exc) {
                return $this->ajaxError($exc->getMessage());
            }
            return $this->ajaxResponse($squad->toArray());
        }
        return $this->ajaxError('Некорректно указаны атрибуты.');
    }

    /**
     * 'game/armies/{id}/buy' - POST AJAX запрос для покупки воинов в армию.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postArmyBuy($id)
    {
        if (Input::has('count')) {
            try {
                $army = Army::find($id);
                $army->buy(Input::get('count'));
            } catch (\Exception $exc) {
                return $this->ajaxError($exc->getMessage());
            }
            return $this->ajaxResponse(['army' => $army, 'squads' => $army->squads]);
        }
        return $this->ajaxError('Некорректно указаны атрибуты.');
    }

    /**
     * 'game/armies/{id}/upgrade' - POST AJAX запрос для улучшения армии.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postArmyUpgrade($id)
    {
        try {
            $army = Army::find($id);
            $army->upgrade();
        } catch (\Exception $exc) {
            return $this->ajaxError($exc->getMessage());
        }
        return $this->ajaxResponse(['army' => $army, 'squads' => $army->squads]);
    }
}