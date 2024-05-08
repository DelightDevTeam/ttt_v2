<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthWinLotteryPrizeService
{
    public function LotteryWinnersPrize()
    {
        try {
            // Define the start and end of today
            $todayStart = Carbon::today()->startOfDay(); // Start of today
            $tomorrowStart = Carbon::tomorrow()->startOfDay(); // Start of tomorrow (for end of today)

            // Define session time ranges
            $morningStart = '04:01:00';
            $morningEnd = '16:10:00';
            $eveningStart = '16:05:00';
            $eveningEnd = '2:45:00';

            // Retrieve today's prize-sent results with adjusted conditions
            $results = DB::table('lottery_two_digit_pivot')
                ->join('users', 'lottery_two_digit_pivot.user_id', '=', 'users.id')
                ->select(
                    'users.name as user_name',
                    'users.phone as user_phone',
                    'users.profile as user_profile',
                    'lottery_two_digit_pivot.bet_digit',
                    'lottery_two_digit_pivot.res_date',
                    'lottery_two_digit_pivot.sub_amount',
                    'lottery_two_digit_pivot.session',
                    'lottery_two_digit_pivot.res_time',
                    'lottery_two_digit_pivot.prize_sent'
                )
                // Only include prize-sent records
                ->where('lottery_two_digit_pivot.prize_sent', true)
                // Ensure it's today's date
                ->whereBetween('lottery_two_digit_pivot.res_date', [$todayStart, $tomorrowStart])
                // Consider session time ranges
                ->where(function ($query) use ($morningStart, $morningEnd, $eveningStart, $eveningEnd) {
                    $query->whereBetween('lottery_two_digit_pivot.res_time', [$morningStart, $morningEnd])
                        ->orWhereBetween('lottery_two_digit_pivot.res_time', [$eveningStart, $eveningEnd]);
                })
                ->get();

            // Calculate the total prize amount for today
            $totalPrizeAmount = 0;
            foreach ($results as $result) {
                $prizeAmount = $result->sub_amount * 80; // Assuming 80x multiplier
                $totalPrizeAmount += $prizeAmount;
            }

            return ['results' => $results, 'totalPrizeAmount' => $totalPrizeAmount];
        } catch (\Exception $e) {
            Log::error("Error in LotteryWinnersPrize: {$e->getMessage()}");

            return ['results' => collect([]), 'totalPrizeAmount' => 0];
        }
    }
}