<?php

namespace App\Http\Services;

use App\Models\Championship;
use App\Models\ChampionshipTeam;
use App\Models\Game;
use App\Models\Team;
use Illuminate\Http\Request;

class ChampionshipService extends Service
{
    public $championship_id;

    public function __construct(int $championship_id)
    {
        $this->championship_id = $championship_id;
    }

    public function getChampionshipInfo(): array
    {
        $podium = $this->getChampionshipPodium();
        $data['championship'] = [
            'name'      => Championship::where('id',$this->championship_id)->first()->name,
            'winner'        => !empty($podium['first']) ? Team::where('id',$podium['first'])->first()->name : 'A definir',
            'second_place'  => !empty($podium['second']) ? Team::where('id',$podium['second'])->first()->name  : 'A definir',
            'third_place'   => !empty($podium['third']) ? Team::where('id',$podium['third'])->first()->name : 'A definir',
            'teams'         => $this->getChampionshipTeams()
        ];

        return $data;
    }

    public function getChampionshipPodium(): array
    {
        $podium = Game::where('championship_id', $this->championship_id)->whereIn('type', array('final','third'))->get();

        foreach($podium as $places) {
            if($places->type == 'final') {
                $data['first'] = $places->winner;
                $data['second'] = $places->loser;
            } else {
                $data['third'] = $places->winner;
            }
        }

        return $data ?? [];
    }

    public function getChampionshipTeams()
    {
        $teams = ChampionshipTeam::where('championship_id', $this->championship_id)->get();

        foreach ($teams as $team) {
            $data[] = [
                'team'      => Team::where('id',$team->team_id)->first()->name,
                'points'    => $team->points
            ];
        }

        return $data ?? [];
    }
}
