<?php

namespace App\Http\Services;

use App\Models\ChampionshipTeam;
use App\Models\Game;

class GameService extends Service
{
    public function runGames($matches, $championship_id): void
    {
        foreach($matches as $match) {
            $score = explode(' ',$this->getScore());

            $result = $this->getGameWinner($score, $match->team_1, $match->team_2);

            Game::findOrFail($match->id)->update([
                'winner'    => $result['winner'],
                'loser'     => $result['loser'],
                'score'     => $score[0] . '-' . $score[1],
            ]);

            $this->updatePointsAfterGame($result, $score, $championship_id);
        }

    }

    public function updatePointsAfterGame(array $result, array $score, $championship_id): void
    {
        ChampionshipTeam::where('team_id', $result['winner'])->update([
            'points' => ChampionshipTeam::where('team_id',$result['winner'])->where('championship_id', $championship_id)->first()->points + $score[0]
        ]);

        ChampionshipTeam::where('team_id', $result['loser'])->update([
            'points'        => ChampionshipTeam::where('team_id',$result['loser'])->where('championship_id', $championship_id)->first()->points + $score[1],
            'eliminated'    => 1
        ]);
    }

    public function getGameWinner(array $score, int $team_1, int $team_2): array
    {
        $score_team_1 = $score[0];
        $score_team_2 = $score[1];

        switch($score_team_1){
            case($score_team_1 > $score_team_2):
                return [
                    'winner'    => $team_1,
                    'loser'     => $team_2
                ];
            case($score_team_2 > $score_team_1):
                return [
                    'winner'    => $team_2,
                    'loser'     => $team_1
                ];
            case($score_team_1 == $score_team_2):
                if(ChampionshipTeam::where('team_id', $team_2)->first()->points > ChampionshipTeam::where('team_id', $team_1)->first()->points) {
                    return [
                        'winner'    => $team_2,
                        'loser'     => $team_1
                    ];
                } else {
                    return [
                        'winner'    => $team_1,
                        'loser'     => $team_2
                    ];
                }
        }
    }
}
