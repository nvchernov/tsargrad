<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', function () {
    return view('welcome');
});


Route::resource('news', 'NewsController');

// Комментарии
Route::post('comments/add', 'CommentBlocksController@add');
Route::get('comments/{id}', 'CommentBlocksController@index');

// Единый игровой контроллер.
Route::get('game', ['uses' => 'GameController@index', 'as' => 'gamefield']);
Route::put('game/army/crusade', 'GameController@armyCrusade');
Route::put('game/army/buy', 'GameController@armyBuy');
Route::put('game/army/upgrade', 'GameController@armyUpgrade');