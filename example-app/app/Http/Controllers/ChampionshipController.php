<?php

namespace App\Http\Controllers;

use App\Http\Services\ChampionshipService;
use App\Models\Championship;
use Illuminate\Http\Request;

class ChampionshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): object
    {
        return Championship::paginate(5);
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

        if(!empty(Championship::where('name', $request->name)->first())) {
            return response()->json(['error'=>'Championship already exist!'],400);
        }

        $championship = Championship::firstOrCreate([
            'name' => $request->name
        ]);

        return $championship;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Championship  $championship
     * @return \Illuminate\Http\Response
     */
    public function show(Championship $championship, int $championship_id): object
    {
        if(Championship::where('id',$championship_id)->first()) {
            $service = new ChampionshipService($championship_id);

            return response()->json(['success'=>'true',
                                'data'  => $service->getChampionshipInfo()], 200);
        }

        return response()->json(['error'=>'Championship not found'], 400);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Championship  $championship
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $championship_id): object
    {
        if(Championship::where('id',$championship_id)->first()) {
            $request->validate([
                'name'    => 'required|string',
            ]);

            Championship::findOrFail($championship_id)->update(['name' => $request->name]);

            return Championship::findOrFail($championship_id);
        }

        return response()->json(['error'=>'Championship not found']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Championship  $championship
     * @return \Illuminate\Http\Response
     */
    public function destroy(Championship $championship, int $championship_id): object
    {
        if(!empty($championship_id) && !empty(Championship::where('id', $championship_id)->first())) {
           Championship::findOrFail($championship_id)->delete();

           return response()->json(['success'=>'deleted']);
        }

        return response()->json(['error'=>'Championship not found']);

    }
}
