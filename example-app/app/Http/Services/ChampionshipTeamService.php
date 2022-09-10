<?php

namespace App\Http\Services;

use App\Models\ChampionshipTeam;
use App\Models\Game;
use App\Models\Team;

class ChampionshipTeamService extends Service
{
    public function getById(int $championship_id): object
    {
        return ChampionshipTeam::where('championship_id', $championship_id)->get();
    }

    public function getCountAvaibleChampionshipGames(int $championship_id): int
    {
        return count(ChampionshipTeam::where('championship_id',$championship_id)->where('eliminated', null)->get());
    }

    public function getAvaibleChampionshipGames(int $championship_id): object
    {
        return ChampionshipTeam::where('championship_id',$championship_id)->where('eliminated', null)->get();
    }

    public function validateEntryTeams(array $teams_to_insert, int $championship_id): int
    {
        $count_teams_championship = 8 - $this->getCountAvaibleChampionshipGames($championship_id);

        if(count($teams_to_insert) <= $count_teams_championship) {
            foreach($teams_to_insert as $team) {
                $team_verify = Team::where('id', $team['team'])->orWhere('name', $team['team'])->first();

                if(!empty($team_verify)) {
                    $team_already_in = ChampionshipTeam::where('championship_id', $championship_id)->where('team_id', $team_verify->id)->first();

                    if(!empty($team_already_in)) {
                        return 3;
                    } else {
                        return 0;
                    }
                } else {
                    return 2;
                }
            }
        } else {
            return 1;
        }
    }

    public function insertTeams(array $teams, int $championship_id): void
    {
        foreach($teams as $team) {

            $team = Team::where('id', $team['team'])->orWhere('name', $team['team'])->first();

            ChampionshipTeam::firstOrCreate([
                'team_id'           => $team->id,
                'championship_id'   => $championship_id
            ]);
        }
    }

    public function validateChampionshipStage(int $championship_id):bool
    {
        if(!empty($this->getChampionshipStage($championship_id))) {
            if($this->verifyIfChampionshipAlreadyStart($this->getChampionshipStage($championship_id), $championship_id)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        };
    }

    public function verifyIfChampionshipAlreadyStart($championship_stage, $championship_id)
    {
        if(!empty( Game::where('type', $championship_stage)->where('championship_id', $championship_id)->where('winner', '=' ,NULL)->first())) {
            return false;
        } else {
            return true;
        }
    }

    public function sortAndCreateGames(int $championship_id): void
    {
            $championship_team  = $this->getAvaibleChampionshipGames($championship_id);

            foreach($championship_team as $teams) {
                $team_ids[] = $teams['team_id'];
            }

            shuffle($team_ids);

            $this->createChampionshipGames($team_ids, $this->getChampionshipStage($championship_id), $championship_id);
    }

    public function getChampionshipStage(int $championship_id): string
    {
        switch($this->getCountAvaibleChampionshipGames($championship_id)){
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

    public function createChampionshipGames(array $team_ids, string $championship_stage, int $championship_id): void
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

    public function getThirdPlace(int $championship_id): void
    {
        $semifinal_games = Game::where('championship_id', $championship_id)->where('type', 'semi')->get();

        foreach($semifinal_games as $game) {
            $third_place_game[] = $game->winner == $game->team_1 ? $game->team_2 : $game->team_1;
        }

        $score = explode(' ',$this->getScore());

        $result = $this->getGameWinner($score, $third_place_game[0], $third_place_game[1]);

        Game::firstOrCreate([
            'team_1'            => $third_place_game[0],
            'team_2'            => $third_place_game[1],
            'type'              => 'third',
            'championship_id'   => $championship_id,
            'winner'            => $result['winner'],
            'loser'             => $result['loser'],
            'score'             => $score[0] . '-' . $score[1],
        ]);

        $this->updatePointsAfterGame($result, $score, $championship_id);
    }

    public function updatePointsAfterGame(array $result, array $score, $championship_id): void
    {
        ChampionshipTeam::where('team_id', $result['winner'])->update([
            'points' => ChampionshipTeam::where('team_id',$result['winner'])->where('championship_id', $championship_id)->first()->points + $score[0] - $score[1]
        ]);

        ChampionshipTeam::where('team_id', $result['loser'])->update([
            'points'        => ChampionshipTeam::where('team_id',$result['loser'])->where('championship_id', $championship_id)->first()->points + $score[1] - $score[0],
            'eliminated'    => 1
        ]);
    }
}
