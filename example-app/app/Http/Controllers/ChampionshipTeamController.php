<?php

namespace App\Http\Controllers;

use App\Models\ChampionshipTeam;
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

    }

    public function insertTeamsOnAChampionship(Request $request, int $championship_id)
    {
        $request->validate([
            'teams.*.team'    => 'required',
        ]);

        foreach($request->teams as $team) {

            $team =  Team::where('id', $team['team'])->orWhere('name', $team['team'])->first();

            ChampionshipTeam::firstOrCreate([
                'team_id'           => $team->id,
                'championship_id'   => $championship_id
            ]);
        }

        return ChampionshipTeam::where('championship_id', $championship_id)->get();
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
