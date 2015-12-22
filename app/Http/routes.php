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
    return view('auth/login');
});

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// General page
Route::get('home', [
    'middleware' => 'auth',
    'uses' => 'HomeController@home'
]);

Route::get('user/profile', [
    'middleware' => 'auth',
    'uses' => 'UserController@getProfile'
]);
Route::post('user/update', [
    'middleware' => 'auth',
    'uses' => 'UserController@postUpdate'
]);

// General page
Route::get('home', [
    'middleware' => 'auth',
    'uses' => 'HomeController@home'
]);

Route::get('user/profile', [
    'middleware' => 'auth',
    'uses' => 'UserController@getProfile'
]);
Route::post('user/update', [
    'middleware' => 'auth',
    'uses' => 'UserController@postUpdate'
]);

Route::resource('news', 'NewsController');

// Комментарии
Route::post('comments/add', 'CommentBlocksController@add');
Route::get('comments/{id}', 'CommentBlocksController@index');

// Единый игровой контроллер.
Route::get('game', ['uses' => 'GameController@index', 'as' => 'gamefield']);
Route::put('game/army/crusade', 'GameController@armyCrusade');
Route::put('game/army/buy', 'GameController@armyBuy');
Route::put('game/army/upgrade', 'GameController@armyUpgrade');
