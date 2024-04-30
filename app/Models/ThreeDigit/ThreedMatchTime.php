<?php

namespace App\Models\ThreeDigit;

use App\Models\Admin\ThreedLottery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThreedMatchTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'open_time',
        'match_time',
    ];

  
}