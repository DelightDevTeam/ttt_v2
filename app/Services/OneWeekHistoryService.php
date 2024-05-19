<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\ThreeDigit\Lotto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class OneWeekHistoryService
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
                $start = Carbon::create($today->year, $currentMonth, 1);
                $end = Carbon::create($today->year, $currentMonth, 16);
            } else {
                $start = Carbon::create($today->year, $currentMonth, 17);
                $end = Carbon::create($today->year, $currentMonth, 1)->addMonth();
            }
        }

        return [$start, $end];
    }

    /**
     * Retrieve user played data within the given date range.
     *
     * @return array
     */
    public function getUserData()
    {
        $userId = Auth::id(); // Authenticated user's ID
        [$startDate, $endDate] = $this->getDateRangeForMonth(); // Get the date range

        Log::info("Retrieving data for user ID: $userId, from $startDate to $endDate");

        // Retrieve lottos for the user in the given date range
        $lottos = Lotto::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user') // Eager load user details
            ->get();

        if ($lottos->isEmpty()) {
            Log::info("No lottos found for user ID: $userId in the given date range.");

            return [
                'results' => collect([]),
                'totalSubAmount' => 0,
            ];
        }

        // Retrieve the user's played data within the specified date range
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
            ->where('lotto_three_digit_pivot.user_log', 'open')
            ->whereIn('lotto_three_digit_pivot.lotto_id', $lottos->pluck('id')) // Use lottery IDs
            ->get();

        Log::info("Retrieved results for user ID: $userId, results count: ".$results->count());

        // Calculate the total sub_amount for this user within the relevant date range
        $totalSubAmount = DB::table('lotto_three_digit_pivot')
            ->whereIn('lotto_three_digit_pivot.lotto_id', $lottos->pluck('id')) // Use lottery IDs
            ->whereBetween('lotto_three_digit_pivot.created_at', [$startDate, $endDate])
            ->sum('sub_amount');

        Log::info("Total sub_amount for user ID: $userId is $totalSubAmount");

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
        ];
    }
}
