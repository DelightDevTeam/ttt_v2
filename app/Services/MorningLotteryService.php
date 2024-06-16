<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MorningLotteryService
{
    /**
     * Determine the current session based on the time of day.
     *
     * @return string
     */
    protected function getCurrentSession()
    {
        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '01:01:00' && $currentTime <= '12:01:00') {
            return 'morning'; // Morning session
        } else {
            return 'closed'; // If outside session time
        }
    }

    /**
     * Get data for lotteries with an admin log status of "open" for today's date and current session, filtered by the authenticated user.
     *
     * @return array
     */
    public function MorningHistory()
    {
        $today = Carbon::today()->toDateString(); // Get today's date
        //$currentSession = $this->getCurrentSession(); // Get the current session
        $userId = Auth::id(); // Get the authenticated user's ID

        //Log::info("Retrieving data for user ID: $userId, for the current session");

        // Fetch lottery IDs for the authenticated user within the current session
        $lotteryIds = DB::table('lottery_two_digit_pivot')
            ->where('user_id', $userId) // Filter by the authenticated user's ID
            ->where('session', 'morning') // Filter by session
            //->where('user_log', 'open') // Ensure user log is open
            ->pluck('lottery_id'); // Get unique lottery IDs

        if ($lotteryIds->isEmpty()) {
            Log::info("No lotteries found for user ID: $userId during the current session.");

            return [
                'results' => collect([]), // Return an empty collection
                'totalSubAmount' => 0,
            ];
        }

        // Fetch the user's data based on the retrieved lottery IDs
        $results = DB::table('lottery_two_digit_pivot')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->join('users', 'lotteries.user_id', '=', 'users.id')
            ->select(
                'users.name as user_name',
                'users.phone as user_phone',
                'lottery_two_digit_pivot.bet_digit',
                'lottery_two_digit_pivot.res_date',
                'lottery_two_digit_pivot.res_time',
                'lottery_two_digit_pivot.sub_amount',
                'lottery_two_digit_pivot.prize_sent',
                'lottery_two_digit_pivot.match_status'
            )
            ->where('lottery_two_digit_pivot.res_date', $today) 
            ->whereIn('lottery_two_digit_pivot.lottery_id', $lotteryIds) 
            ->orderBy('res_date', 'desc')
            ->get();

        // Calculate the total sub_amount for this session and user
        $totalSubAmount = DB::table('lottery_two_digit_pivot')
            ->whereIn('lottery_two_digit_pivot.lottery_id', $lotteryIds) // Filter by the user's lottery IDs
            //->where('user_log', 'open') // Ensure user log is open
            ->where('session', 'morning') // Filter by session

            ->sum('sub_amount');

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
        ];
    }
}