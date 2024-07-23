<?php

namespace App\Models\Admin;

use App\Models\Admin\ThreedLottery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreedMatchTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'result_date',
        'result_time',
        'match_time',
        //'run_match',
        'status',
    ];

    // public function threedLotteries()
    // {
    //     return $this->belongsToMany(ThreedLottery::class, 'lottery_match_pivot', 'threed_match_time_id', 'threed_lottery_id');
    // }
}
