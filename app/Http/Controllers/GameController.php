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
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;

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
        $this->middleware('ajax', ['only' => ['armyCrusade', 'armyBuy', 'armyUpgrade']]);
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
     * 'game/' - главная страница игры, игровая карта.
     */
    public function index()
    {
        $data = [];

        $user = $data['user'] = Auth::user();
        $data['castles'] = Castle::with('location')->get();

        //require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/game/index.php');
        return view('game/index', $data);
    }

    /**
     * Ajax success response.
     *
     * @param array $data
     * @param int $code
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    private function ajaxResponse(array $data, $code = 200, $headers = [])
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
    private function ajaxError($msg, $code = 500, $headers = [])
    {
        return response()->json(['success' => false, 'message' => $msg, 'code' => $code], $code, $headers);
    }

    /**
     * 'game/army/crusade - POST AJAX запрос на создание нового отряда для похода.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function armyCrusade(Request $request)
    {
        $user = $request->user();
        $name = $request->input('name');
        $goal_id = $request->input('goal_id');
        $count = $request->input('count');

        if (isset($user) && !empty($name) && !empty($goal_id) && !empty($count)) {
            try {
                $goal = Castle::find($request->input('goal_id'));
                $army = $user->castle->army;
                $squad = $army->crusade($request->input('name'), $request->input('count'), $goal);
            } catch (Exception $exc) {
                return $this->ajaxError($exc->getMessage());
            }
            return $this->ajaxResponse($squad->toArray());
        }

        return $this->ajaxError('Некорректно указаны атрибуты');
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