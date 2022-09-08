<?php

namespace App\Http\Controllers;

use App\Http\Services\GameService;
use App\Models\Game;
class GameController extends Controller
{

    public function runGames(int $championship_id)
    {
        $matches = Game::where('championship_id', $championship_id)->where('winner', NULL)->get();

        if(empty($matches)) {
            return response()->json([
                'error'         =>'O campeonato já terminou!',
                'games_played'  => Game::where('championship_id', $championship_id)->get()
            ], 400);
        }

        $service = new GameService;
        $service->runGames($matches, $championship_id);

        return Game::where('championship_id', $championship_id)->get();
    }
}
