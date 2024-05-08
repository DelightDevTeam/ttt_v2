<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EveningLotteryService
{
    protected function getCurrentSession()
    {
        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '12:01:00' && $currentTime <= '16:30:00') {
            return 'evening';
        } else {
            return 'closed';
        }
    }

    public function TwoDEveningHistory()
    {
        // Log current session and user ID for verification
        //Log::info('Current time:', ['time' => Carbon::now()->format('H:i:s')]);
        //Log::info('User ID:', ['user_id' => Auth::id()]);
        $today = Carbon::today()->toDateString(); // Get today's date
        $currentSession = $this->getCurrentSession();
        $userId = Auth::id();

        // Ensure the session is 'evening'
        if ($currentSession !== 'evening') {
            Log::info("Session is not 'evening'. Current session: $currentSession");

            return [
                'results' => collect([]),
                'totalSubAmount' => 0,
            ];
        }

        Log::info("Retrieving data for user ID: $userId, for the current session");

        // Retrieve lottery IDs for the authenticated user in the current session
        $lotteryIds = DB::table('lottery_two_digit_pivot')
            ->where('user_id', $userId)
            ->where('session', 'evening')
            ->pluck('lottery_id');

        if ($lotteryIds->isEmpty()) {
            Log::info("No lotteries found for user ID: $userId during the current session.");

            return [
                'results' => collect([]),
                'totalSubAmount' => 0,
            ];
        }

        // Fetch results based on the retrieved lottery IDs
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
            ->whereIn('lottery_two_digit_pivot.lottery_id', $lotteryIds)
            ->where('session', 'evening')
            ->sum('sub_amount');

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
        ];
    }
}