<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 21.12.2015
 * Time: 0:25
 */

namespace App\Http\Controllers;

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
        //$this->middleware('auth');
        $this->middleware('game.army');
        $this->middleware('ajax', ['only' => ['armyCrusade', 'armyBuy', 'armyUpgrade']]);
        $this->middleware('json', ['only' => ['armyCrusade', 'armyBuy', 'armyUpgrade']]);
    }

    /**
     * 'game/' - главная страница игры, игровая карта.
     */
    public function index()
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/game/index.php');
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