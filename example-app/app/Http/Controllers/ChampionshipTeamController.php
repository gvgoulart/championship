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

    public function getCountAvaibleChampionshipGames(int $championship_id): int
    {
        return count(ChampionshipTeam::where('championship_id',$championship_id)->where('eliminated', null)->get());
    }

    public function getAvaibleChampionshipGames(int $championship_id): object
    {
        return ChampionshipTeam::where('championship_id',$championship_id)->where('eliminated', null)->get();
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

        $count_teams_championship = 8 - $this->getCountAvaibleChampionshipGames($championship_id);

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

        return $this->getById($championship_id);
    }

    public function sortAndCreateGames(int $championship_id): object
    {
        if(empty(Championship::where('id', $championship_id)->first())) {
            return response()->json([
                'error'  =>'O campeonato não existe.',
            ]);
        }

        $championship_team  = $this->getAvaibleChampionshipGames($championship_id);
        $championship_stage = $this->getChampionshipStage($championship_id);

        if(!empty($championship_stage)) {

            if($championship_stage == 'final') {
                $this->getThirdPlace($championship_id);
            }

            foreach($championship_team as $teams) {
                $team_ids[] = $teams['team_id'];
            }
            shuffle($team_ids);

            $this->createChampionshipGames($team_ids, $championship_stage, $championship_id);

            return response()->json([
                'success'   =>"Partidas da $championship_stage criadas!",
                'games'     => Game::where('championship_id', $championship_id)->get()
            ]);

        } else {
            return response()->json([
                'error'             =>'O campeonato já terminou!',
                'teams_already_in'  => $this->getChampionshipStage($championship_id)
            ]);
        }
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

    public function runGames(int $championship_id)
    {
        $matches = Game::where('championship_id', $championship_id)->where('winner', NULL)->get();

        foreach($matches as $match) {
            $score = explode(' ',$this->getScore());

            $result = $this->getGameWinner($score, $match->team_1, $match->team_2);

            Game::findOrFail($match->id)->update([
                'winner'    => $result['winner'],
                'score'     => $score[0] . '-' . $score[1],
            ]);

            $this->updatePointsAfterGame($result, $score, $championship_id);
        }

        return Game::where('championship_id', $championship_id)->get();
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
            'score'             => $score[0] . '-' . $score[1],
        ]);

        $this->updatePointsAfterGame($result, $score, $championship_id);
    }

    public function updatePointsAfterGame(array $result, array $score, $championship_id): void
    {
        ChampionshipTeam::where('team_id', $result['winner'])->update([
            'points' => ChampionshipTeam::where('team_id',$result['winner'])->where('championship_id', $championship_id)->first()->points + $score[0]
        ]);

        ChampionshipTeam::where('team_id', $result['loser'])->update([
            'points'        => ChampionshipTeam::where('team_id',$result['winner'])->where('championship_id', $championship_id)->first()->points + $score[1],
            'eliminated'    => 1
        ]);
    }
}
