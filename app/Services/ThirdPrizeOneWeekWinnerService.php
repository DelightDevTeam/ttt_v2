<?php

namespace App\Services;

use App\Models\ThreeDigit\Lotto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThirdPrizeOneWeekWinnerService
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
            if ($currentDay <= 16) {
                $start = Carbon::create($today->year, 12, 1);
                $end = Carbon::create($today->year, 12, 16);
            } else {
                $start = Carbon::create($today->year, 12, 17);
                $end = Carbon::create($today->year, 12, 30);
            }
        } else {
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
     * Retrieve all user data within the specified date range.
     *
     * @return array
     */
    public function OneWeekThirdPrizeWinner()
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
                'totalPrizeAmount' => 0, // Default total prize amount to zero
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
            ->where('lotto_three_digit_pivot.prize_sent', 3)
            ->whereIn('lotto_three_digit_pivot.lotto_id', $lottos->pluck('id'))
            ->whereBetween('lotto_three_digit_pivot.created_at', [$startDate, $endDate])
            ->get();

        Log::info('Retrieved results count: '.$results->count());

        // Calculate the total sub_amount and total prize amount
        $totalSubAmount = DB::table('lotto_three_digit_pivot')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('sub_amount');

        $totalPrizeAmount = 0; // Initialize total prize amount
        foreach ($results as $result) {
            $prizeAmount = $result->sub_amount * 10; // Prize multiplier
            $result->prize_amount = $prizeAmount; // Add prize amount to each result
            $totalPrizeAmount += $prizeAmount; // Accumulate total prize amount
        }

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
            'totalPrizeAmount' => $totalPrizeAmount,
        ];
    }
}
