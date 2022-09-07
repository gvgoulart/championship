<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\ChampionshipTeam;
use App\Models\Game;
use App\Models\Team;
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getById(int $championship_id): object
    {
        return ChampionshipTeam::where('championship_id', $championship_id)->get();
    }


    /**
     * insertTeamsOnAChampionship validate and insert teams on a championship.
     *
     * @return object
     */
    public function insertTeamsOnAChampionship(Request $request, int $championship_id): object
    {
        $request->validate([
            'teams'         => 'required',
            'teams.*.team'  => 'required'
        ]);

        $championship_team = $this->getById($championship_id);

        $count_teams_championship = 8 - count(ChampionshipTeam::where('championship_id', $championship_id)->get());

        if(count($request->teams) <= $count_teams_championship) {
            foreach($request->teams as $team) {

                $team = Team::where('id', $team['team'])->orWhere('name', $team['team'])->first();

                ChampionshipTeam::firstOrCreate([
                    'team_id'           => $team->id,
                    'championship_id'   => $championship_id
                ]);
            }
        } else {
            return response()->json([
                'error'             =>'O campeonato não suporta essa quantidade de times.',
                'teams_already_in'  => $championship_team
            ]);
        }

        return ChampionshipTeam::where('championship_id', $championship_id)->get();
    }

    public function sortAndCreateGames(int $championship_id): object
    {
        if(empty(Championship::where('id', $championship_id)->first())) {
            return response()->json([
                'error'  =>'O campeonato não existe.',
            ]);
        }

        $championship_team  = $this->getById($championship_id);
        $championship_stage = $this->getChampionshipStage($championship_id);

        if(!empty($championship_stage)) {

            foreach($championship_team as $key => $teams) {
                $team_ids[] = $teams['team_id'];
            }
            shuffle($team_ids);

            $this->createChampionshipGames($team_ids, $championship_stage, $championship_id);

            return response()->json([
                'success'   =>"Partidas da $championship_stage criadas!",
                'games'     => Game::where('championship_id', $championship_id)
            ]);

        } else {
            return response()->json([
                'error'             =>'O campeonato só pode começar com 8 times.',
                'teams_already_in'  => $this->getChampionshipStage($championship_id)
            ]);
        }
    }

    public function getChampionshipStage(int $championship_id)
    {
        switch(count(ChampionshipTeam::where('championship_id',$championship_id)->get())){
            case 8:
                return 'quarter';
            case 4:
                return 'semi';
            case 2:
                return 'final';
            default:
                return '';
        }
    }

    public function createChampionshipGames(array $team_ids, string $championship_stage, int $championship_id)
    {
        for ($i = 0; count($team_ids) > 0; $i++) {
            $team_1 = $team_ids[$i];
            unset($team_ids[$i]);

            $i++;

            $team_2 = $team_ids[$i];
            unset($team_ids[$i]);

            Game::firstOrCreate([
                'team_1'            => $team_1,
                'team_2'            => $team_2,
                'type'              => $championship_stage,
                'championship_id'   => $championship_id
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChampionshipTeam  $championshipTeam
     * @return \Illuminate\Http\Response
     */
    public function show(ChampionshipTeam $championshipTeam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChampionshipTeam  $championshipTeam
     * @return \Illuminate\Http\Response
     */
    public function edit(ChampionshipTeam $championshipTeam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChampionshipTeam  $championshipTeam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChampionshipTeam $championshipTeam)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChampionshipTeam  $championshipTeam
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChampionshipTeam $championshipTeam)
    {
        //
    }
}