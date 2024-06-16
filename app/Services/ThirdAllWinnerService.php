<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\ThreeD\ThreedSetting;
use App\Models\ThreeDigit\ResultDate;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;

class ThirdAllWinnerService
{
    public function ThirdWinner()
    {
        

        // Retrieve records within the specified date range and include user information
        $records = LotteryThreeDigitPivot::with('user')
            ->where('prize_sent', 3)
            ->get();

        // Calculate the total sub_amount
        $total_sub_amount = $records->sum('sub_amount');

        // Return the records and total sub_amount
        return [
            'records' => $records,
            'total_sub_amount' => $total_sub_amount,
        ];
    }
}
