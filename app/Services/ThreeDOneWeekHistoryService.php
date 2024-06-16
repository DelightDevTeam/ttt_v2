<?php

namespace App\Services;

use App\Models\ThreeDigit\Lotto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThreeDOneWeekHistoryService
{
    /**
     * Determine the date range based on the current month and day.
     *
     * @return array
     */
    protected function getDateRangeForMonth()
    {
        $today = Carbon::now();
        $currentDay = $today->day;
        $currentMonth = $today->month;

        if ($currentMonth === 12) {
            // December has 1st to 16th and 17th to 30th
            if ($currentDay <= 16) {
                $start = Carbon::create($today->year, 12, 1);
                $end = Carbon::create($today->year, 12, 16);
            } else {
                $start = Carbon::create($today->year, 12, 17);
                $end = Carbon::create($today->year, 12, 30);
            }
        } else {
            // Other months have 1st to 16th and 17th to 1st of the next month
            if ($currentDay <= 16) {
                $start = Carbon::create($today->year, $currentMonth, 2);
                $end = Carbon::create($today->year, $currentMonth, 16);
            } else {
                $start = Carbon::create($today->year, $currentMonth, 17);
                $end = Carbon::create($today->year, $currentMonth, 1)->addMonth();
            }
        }

        return [$start, $end];
    }

    /**
     * Retrieve all user play data within the specified date range.
     *
     * @return array
     */
    public function getAllUserData()
    {
        [$startDate, $endDate] = $this->getDateRangeForMonth(); // Get the date range

        Log::info("Retrieving all user data from $startDate to $endDate");

        // Retrieve lottos created within the given date range
        $lottos = Lotto::whereBetween('created_at', [$startDate, $endDate])
            ->with('user') // Eager load user details
            ->get();

        if ($lottos->isEmpty()) {
            Log::info('No lottos found in the given date range.');

            return [
                'results' => collect([]),
                'totalSubAmount' => 0,
            ];
        }

        // Retrieve played data within the specified date range for all users
        $results = DB::table('lotto_three_digit_pivot')
            ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
            ->join('users', 'lottos.user_id', '=', 'users.id')
            ->select(
                'users.name as user_name',
                'users.phone as user_phone',
                'lotto_three_digit_pivot.bet_digit',
                'lotto_three_digit_pivot.res_date',
                'lotto_three_digit_pivot.res_time',
                'lotto_three_digit_pivot.sub_amount',
                'lotto_three_digit_pivot.prize_sent',
                'lotto_three_digit_pivot.match_status',
                'lotto_three_digit_pivot.match_start_date'
            )
            ->whereBetween('lotto_three_digit_pivot.created_at', [$startDate, $endDate])
            ->get();

        Log::info('Retrieved results count: '.$results->count());

        // Calculate the total sub_amount for all users within the given date range
        $totalSubAmount = DB::table('lotto_three_digit_pivot')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('sub_amount');

        Log::info("Total sub_amount for the specified date range is $totalSubAmount");

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
        ];
    }
}
