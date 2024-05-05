<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminEveningPrizeSentService
{
    /**
     * Determine the current session based on the time of day.
     *
     * @return string
     */
    protected function getCurrentSession()
    {
        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '012:01:00' && $currentTime <= '16:40:00') {
            return 'evening';
        } else {
            return 'closed'; // If outside known session times
        }
    }

    /**
     * Retrieve all user data with prize_sent as true, filtered by session and current day.
     *
     * @return array
     */
    public function getAllUserPrizeSentForAdmin()
    {
        $today = Carbon::today()->toDateString();
        $currentSession = $this->getCurrentSession();

        // If session is closed, return an empty collection
        if ($currentSession === 'closed') {
            return ['results' => collect([]), 'totalPrizeAmount' => 0];
        }

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
                ->where('lottery_two_digit_pivot.res_date', $today)
                ->where('lottery_two_digit_pivot.session', $currentSession)
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
