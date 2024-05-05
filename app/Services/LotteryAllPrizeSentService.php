<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotteryAllPrizeSentService
{
    public function AllWinPrizeSentForAdmin()
    {
        try {
            $results = DB::table('lottery_two_digit_pivot')
                ->join('users', 'lottery_two_digit_pivot.user_id', '=', 'users.id')
                ->select(
                    'users.name as user_name',
                    'users.phone as user_phone',
                    'lottery_two_digit_pivot.bet_digit',
                    'lottery_two_digit_pivot.res_date',
                    'lottery_two_digit_pivot.sub_amount',
                    'lottery_two_digit_pivot.session',
                    'lottery_two_digit_pivot.res_time',
                    'lottery_two_digit_pivot.prize_sent'
                )
                ->where('lottery_two_digit_pivot.prize_sent', true)
                ->get();

            // Calculate total prize amount
            $totalPrizeAmount = 0;
            foreach ($results as $result) {
                $prizeAmount = $result->sub_amount * 85; // Prize multiplier
                $totalPrizeAmount += $prizeAmount;
            }

            return ['results' => $results, 'totalPrizeAmount' => $totalPrizeAmount];

        } catch (\Exception $e) {
            Log::error('Error retrieving prize_sent data: '.$e->getMessage());

            return ['results' => collect([]), 'totalPrizeAmount' => 0];
        }
    }
}
