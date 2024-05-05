<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MorningPrizeSentService
{
    protected function getCurrentSession()
    {
        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:01:00' && $currentTime <= '12:01:00') {
            return 'morning';
        } else {
            return 'closed'; // If outside the known session times
        }
    }

    public function getAuthUserPrizeSentData()
    {
        $userId = Auth::id(); // Get the authenticated user's ID
        $today = Carbon::today()->toDateString(); // Get the current day
        $currentSession = $this->getCurrentSession(); // Determine the current session

        Log::info("Fetching prize_sent data for user ID: {$userId}, Date: {$today}, Session: {$currentSession}");

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
                ->where('lottery_two_digit_pivot.prize_sent', true) // Where prize is sent
                ->where('lottery_two_digit_pivot.user_id', $userId) // Authenticated user
                ->where('lottery_two_digit_pivot.res_date', $today) // Current day
                ->where('lottery_two_digit_pivot.session', $currentSession) // Current session
                ->get();

            $totalPrizeAmount = 0;

            // Calculate the prize amount for each result
            foreach ($results as $result) {
                $prizeAmount = $result->sub_amount * 85; // Prize multiplier
                $result->prize_amount = $prizeAmount; // Add prize_amount to each result
                $totalPrizeAmount += $prizeAmount; // Accumulate total prize amount
            }

            return [
                'results' => $results,
                'totalSubAmount' => $totalPrizeAmount,
            ];

        } catch (\Exception $e) {
            Log::error('Error retrieving prize_sent data: '.$e->getMessage());

            return [
                'results' => collect([]), // Empty collection on error
                'totalSubAmount' => 0,
            ];
        }
    }
}
