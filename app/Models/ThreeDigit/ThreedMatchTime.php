<?php

namespace App\Models\ThreeDigit;

use App\Models\Admin\ThreedLottery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreedMatchTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'open_time',
        'match_time',
    ];
}
