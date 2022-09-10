<?php

namespace App\Http\Controllers;

use App\Http\Services\ChampionshipTeamService;
use App\Models\Championship;
use App\Models\ChampionshipTeam;
use App\Models\Game;
use Illuminate\Http\Request;


class ChampionshipTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ChampionshipTeam::all();
    }

    public function insertTeamsOnAChampionship(Request $request, int $championship_id): object
    {
        $request->validate([
            'teams'         => 'required',
            'teams.*.team'  => 'required'
        ]);

        $service = new ChampionshipTeamService;

        switch($service->validateEntryTeams($request->teams, $championship_id)) {
            case 0:
                $service->insertTeams($request->teams, $championship_id);

                return response()->json([
                    'success'           =>'O campeonato foi criado!',
                    'teams_already_in'  => ChampionshipTeam::where('championship_id', $championship_id)->get()
                ], 201);
            case 1:
                return response()->json([
                    'error'             =>'O campeonato não suporta essa quantidade de times.',
                    'teams_already_in'  => ChampionshipTeam::where('championship_id', $championship_id)->get()
                ], 400);
            case 2:
                return response()->json([
                    'error'             =>'Você esta tentando inserir um time que não existe.',
                    'teams_already_in'  => ChampionshipTeam::where('championship_id', $championship_id)->get()
                ], 400);
            case 3:
                return response()->json([
                    'error'             =>'Você esta tentando inserir um time já esta no campeonato.',
                    'teams_already_in'  => ChampionshipTeam::where('championship_id', $championship_id)->get()
                ], 400);
        }

        return response()->json([
            'success'            => true,
            'teams_already_in'   => $service->getById($championship_id)
        ], 200);
    }

    public function sortAndCreateGames(int $championship_id): object
    {
        if(empty(Championship::where('id', $championship_id)->first())) {
            return response()->json([
                'error'  =>'O campeonato não existe.',
            ], 400);
        }

        $service = new ChampionshipTeamService;

        switch($service->validateChampionshipStage($championship_id)) {
            case 0:
                if($service->getChampionshipStage($championship_id) == 'final') {
                    $service->getThirdPlace($championship_id);
                }

                $service->sortAndCreateGames($championship_id);

                return response()->json([
                    'success'   =>"Partidas da {$service->getChampionshipStage($championship_id)} criadas!",
                    'games'     => Game::where('championship_id', $championship_id)->get()
                ], 200);
            case 1:
                return response()->json([
                    'error'  =>'O campeonato ainda precisa que os jogos aconteçam para continuar!',
                    'games'  => $service->getById($championship_id)
                ], 400);
            case 2:
                return response()->json([
                    'error'  =>'O campeonato já terminou!',
                    'games'  => $service->getById($championship_id)
                ], 400);

        }
    }
}
