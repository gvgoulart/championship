<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = [
        'team_1',
        'team_2',
        'championship_id',
        'winner',
        'type',
        'score'
    ];
}
