<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::get('/team', 'App\Http\Controllers\TeamController@index');
    Route::get('/team/{id}', 'App\Http\Controllers\TeamController@show');
    Route::post('/team', 'App\Http\Controllers\TeamController@create');
    Route::put('/team/{id}', 'App\Http\Controllers\TeamController@edit');
    Route::delete('/team/{id}', 'App\Http\Controllers\TeamController@destroy');

    Route::get('/championship', 'App\Http\Controllers\ChampionshipController@index');
    Route::get('/championship/{id}', 'App\Http\Controllers\ChampionshipController@show');
    Route::post('/championship', 'App\Http\Controllers\ChampionshipController@create');
    Route::put('/championship/{id}', 'App\Http\Controllers\ChampionshipController@edit');
    Route::delete('/championship/{id}', 'App\Http\Controllers\ChampionshipController@destroy');

    Route::post('/championship/{id}/insertTeams', 'App\Http\Controllers\ChampionshipTeamController@insertTeamsOnAChampionship');
    Route::post('/championship/{id}/sort', 'App\Http\Controllers\ChampionshipTeamController@sortAndCreateGames');

    Route::post('/games/{championship_id}/run', 'App\Http\Controllers\GameController@runGames');



