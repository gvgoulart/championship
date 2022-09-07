<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


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

Route::get('/teste', function (Request $request) {
    $a = shell_exec("python3 /var/www/html/test.py");
    echo '<pre>' , var_dump($a) , '</pre>'; die;
    $process = new Process(["python3 /var/www/html/test.py"]);
    $process->run();

    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    $data = $process->getOutput();

    dd($data);
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


