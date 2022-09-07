<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): object
    {
        return Team::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request): object
    {
        $request->validate([
            'name'    => 'required|string',
        ]);

        $team = Team::firstOrCreate([
            'name' => $request->name
        ]);

        return $team;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team, int $team_id): object
    {
        if(Team::where('id',$team_id)->first()) {
            return Team::where('id',$team_id)->first();
        }

        return response()->json(['error'=>'Team not found']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $team_id): object
    {
        if(Team::where('id',$team_id)->first()) {
            $request->validate([
                'name'    => 'required|string',
            ]);

            Team::findOrFail($team_id)->update(['name' => $request->name]);

            return Team::findOrFail($team_id);
        }

        return response()->json(['error'=>'Team not found']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team, int $team_id): object
    {
        if(!empty($team_id) && !empty(Team::where('id', $team_id)->first())) {
           Team::findOrFail($team_id)->delete();

           return response()->json(['success'=>'deleted']);
        }

        return response()->json(['error'=>'Team not found']);

    }
}
