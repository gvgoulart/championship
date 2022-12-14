<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampionshipTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'championship_id',
        'points',
        'eliminated'
    ];

    public function getPoints()
    {
        return $this->points;
    }

    public function teamNames()
    {
        return $this->hasMany('App\Models\Team');
    }
}
