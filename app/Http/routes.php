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
    return redirect('auth/login');
});

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

Route::get('user/profile/{id}', ['middleware' => 'auth', 'uses' => 'UserController@getProfile']);
Route::post('user/profile/add_comment', ['middleware' => 'auth', 'uses' => 'UserController@addComment']);
Route::post('user/update', ['middleware' => 'auth', 'uses' => 'UserController@postUpdate']);

// Роуты запроса ссылки для сброса пароля
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Роуты сброса пароля
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

Route::post('avatar/get_partial', ['middleware' => 'auth', 'uses' => 'AvatarController@postPartial']);

Route::resource('news', 'NewsController');

// Комментарии
Route::post('comments/add', 'CommentBlocksController@add');
Route::get('comments/{id}/{page}', 'CommentBlocksController@comments');

// Игровой контроллер...

// General page
Route::get('game', ['uses' => 'GameController@getIndex', 'as' => 'home']);
Route::get('surrender',  'GameController@surrender');
Route::get('joinBattle',  'GameController@joinBattle');

Route::get('game/castles/{id}', 'GameController@getCastle');
Route::get('game/armies/{id}', 'GameController@getArmy');
Route::post('game/armies/{id}/crusade', 'GameController@postArmyCrusade');
Route::post('game/armies/{id}/buy', 'GameController@postArmyBuy');
Route::post('game/armies/{id}/upgrade', 'GameController@postArmyUpgrade');

Route::post('game/building/{id}/upgrade', 'GameController@upgradeBuildingLevel');
Route::post('game/castles/{id}/recalc', 'GameController@requestRecalcRes');
Route::post('game/spy/new', 'GameController@buySpy');