<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthWinLotteryPrizeService
{
    public function LotteryWinnersPrize()
    {
        try {
            $today = Carbon::today()->startOfDay(); // Start of the current day
            $tomorrow = Carbon::tomorrow()->startOfDay(); // Start of the next day, for upper boundary
            $userId = Auth::id(); // Get the authenticated user's ID

            // Retrieve results with adjusted conditions, focusing on current day's data
            $results = DB::table('lottery_two_digit_pivot')
                ->join('users', 'lottery_two_digit_pivot.user_id', '=', 'users.id')
                ->select(
                    'users.name as user_name',
                    'users.phone as user_phone',
                    'users.profile as user_profile',
                    'lottery_two_digit_pivot.bet_digit',
                    'lottery_two_digit_pivot.res_date',
                    'lottery_two_digit_pivot.sub_amount',
                    'lottery_two_digit_pivot.res_time',
                    'lottery_two_digit_pivot.prize_sent',
                    'lottery_two_digit_pivot.session'

                )
                // Only include prize-sent records
                ->where('lottery_two_digit_pivot.prize_sent', true)
                // Filter by today's date
                ->whereBetween('lottery_two_digit_pivot.res_date', [$today, $tomorrow]) // Today's data
                // Only consider authenticated user
                ->where('lottery_two_digit_pivot.user_id', $userId)
                ->get();

            // Calculate the total prize amount
            $totalPrizeAmount = 0;
            foreach ($results as $result) {
                $prizeAmount = $result->sub_amount * 80; // Prize multiplier
                $totalPrizeAmount += $prizeAmount; // Accumulate the total prize amount
            }

            return ['results' => $results, 'totalPrizeAmount' => $totalPrizeAmount];
        } catch (\Exception $e) {
            Log::error('Error retrieving prize_sent data: '.$e->getMessage());

            return ['results' => collect([]), 'totalPrizeAmount' => 0];
        }
    }
}
