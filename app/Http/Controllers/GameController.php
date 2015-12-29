<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 21.12.2015
 * Time: 0:25
 */

namespace App\Http\Controllers;

use App\Models\Resource;
use Auth;
use App\Models\Castle;
use App\Models\Army;
use App\Models\Building;
use App\Models\PveEnemyAttack;
use Input;
use Carbon\Carbon;

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
        $this->middleware('ajax', ['except' => ['getIndex', 'upgradeBuildingLevel', 'surrender', 'joinBattle', 'requestRecalcRes']]);
        $this->middleware('wants.json', ['except' => ['getIndex', 'upgradeBuildingLevel', 'surrender', 'joinBattle', 'requestRecalcRes']]);
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
        $data['buildings'] = $c->buildings()->get()->all();
        $attack = $user->lastPveAttack();
        if ( is_null($attack) || $attack->status != 0)
        {
            if ( is_null($attack) )
            {
                $is = rand(0, 10) / 6;
            }
            else
            {
                $is = ( time() - $attack->updated_at->getTimestamp()  ) / 1000 * rand(0, 10);
                var_dump($is);
            }
            if ($is > 1) {
                $resurce_id = rand(1,3);
                $resurce = Resource::find($resurce_id);
                $resource_count = $c->getResources($resurce->name)*rand(1,10)/10;
                $attack = PveEnemyAttack::create([
                    'demanded_resource_count' => $resource_count,
                    'demanded_resource_id' => $resurce_id,
                    'pve_enemy_id' => rand(1,5),
                    'user_id' => $user->id,
                    'status' => 0,
                    'army_count' => $user->army->size * rand(1,200)/100,
                    'army_count' => $user->army->level + rand(-1,1)
                ]);
           }
        }
        // При заходе на карту пересчитываем ресурсы
        $c->calcCastleIncreaseResources();
        $data['attack'] = $attack;
        return view('game/index', $data);
    }

    // Метод вызывающий пересчет ресурсов на уровне сервера для замка с id = $id
    public function requestRecalcRes($id) {
        $castle = Castle::find($id);
        $castle->calcCastleIncreaseResources();
    }

    public function surrender()
    {
        $user =  Auth::user();
        $attack = $user->lastPveAttack();
        if ( $attack->status == 0 )
        {
            $attack->status = 1;
            $resource = Resource::find($attack->demanded_resource_id);
            $user->castle->subResource(
                $resource->name,
                $attack->demanded_resource_count
            );
            $attack->update();
            $user->update();
        }
        return redirect('game');
    }

    public function joinBattle()
    {
        $user =  Auth::user();
        // Препросчет актуальных ресурсов
        $user->castle->calcCastleIncreaseResources();
        $attack = $user->lastPveAttack();
        if ( $attack->status == 0 )
        {
            $result = $user->castle->army->defend($attack->army_level, $attack->army_count);
            $resource = Resource::find($attack->demanded_resource_id);
            if ($result==true)
            {
                $attack->status = 2;
                $user->castle->addResource(
                    $resource->name,
                    $attack->demanded_resource_count
                );
            }
            else
            {
                $attack->status = 1;
                $user->castle->subResource(
                    $resource->name,
                    $attack->demanded_resource_count
                );
            }
        }
        $attack->save();
        $user->save();
        return redirect('game');
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
        $c->calcCastleIncreaseResources();
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
    
    
    public function upgradeBuildingLevel($id) {
        $build = Building::find($id);
        $currentWood = $build->castle()->first()->getResources('wood');
        $currentGold = $build->castle()->first()->getResources('gold');
        $cost = $build->costUpdate();
        if($currentWood >= $cost && $currentGold >= $cost) {
            $build->levelUp();
            $build->castle()->first()->subResource('wood', $cost);
            $build->castle()->first()->subResource('gold', $cost);
            return "success";
        } else {
            return "no_costs";
        }
    }
}