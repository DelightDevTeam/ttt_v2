<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MorningLotteryAdminLogService
{
    /**
     * Determine the current session based on the time of day.
     *
     * @return string
     */
    protected function getCurrentSession()
    {
        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:00:00' && $currentTime <= '12:01:00') {
            return 'morning'; // Morning session
        } else {
            return 'closed'; // Default to closed if outside known session times
        }
    }

    /**
     * Get data for lotteries with an admin log status of "open" for today's date, filtered by current session.
     *
     * @return array
     */
    public function getLotteryAdminLog()
    {
        // Get today's date
        $today = Carbon::today()->toDateString(); // Format 'YYYY-MM-DD'

        // Determine the current session
        $currentSession = $this->getCurrentSession();

        // Query to retrieve the required data
        $results = DB::table('lottery_two_digit_pivot')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->join('users', 'lotteries.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.phone as user_phone',
                'lottery_two_digit_pivot.bet_digit',
                'lottery_two_digit_pivot.res_date',
                'lottery_two_digit_pivot.res_time',
                'lottery_two_digit_pivot.session',
                'lottery_two_digit_pivot.match_status',
                'lottery_two_digit_pivot.sub_amount'
            )
            //->where('lottery_two_digit_pivot.admin_log', 'open') // Admin log is open
            ->where('lottery_two_digit_pivot.res_date', $today) // Today's results
            ->where('lottery_two_digit_pivot.session', $currentSession) // Current session
            ->get();

        // Calculate the total sub_amount for today's open admin log and current session
        $totalSubAmount = DB::table('lottery_two_digit_pivot')
            ->where('admin_log', 'open') // Admin log is open
            ->where('res_date', $today) // Today's results
            ->where('session', $currentSession) // Current session
            ->sum('sub_amount');

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
        ];
    }
}
