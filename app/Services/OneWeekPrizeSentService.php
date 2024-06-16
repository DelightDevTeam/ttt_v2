<?php

namespace App\Services;

use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Lotto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OneWeekPrizeSentService
{
    /**
     * Determine the date range based on the current month and day.
     *
     * @return array
     */
    protected function GetDateRangeMonth()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Default date range for the sessions
        $firstSessionStart = Carbon::create($currentYear, $currentMonth, 3);
        $firstSessionEnd = Carbon::create($currentYear, $currentMonth, 16);

        $secondSessionStart = Carbon::create($currentYear, $currentMonth, 17);
        $secondSessionEnd = Carbon::create($currentYear, $currentMonth + 1, 1);

        // Special case for May
        if ($currentMonth == 5) {
            $firstSessionStart = Carbon::create($currentYear, 5, 3); // Previous month's 17th
            $firstSessionEnd = Carbon::create($currentYear, 5, 16); // 2nd of May

            $secondSessionStart = Carbon::create($currentYear, 5, 17); // 17th of May
            $secondSessionEnd = Carbon::create($currentYear, 6, 1); // 1st of June
        }

        // Special case for December
        if ($currentMonth == 12) {
            $firstSessionStart = Carbon::create($currentYear, 12, 3);
            $firstSessionEnd = Carbon::create($currentYear, 12, 16);

            $secondSessionStart = Carbon::create($currentYear, 12, 17);
            $secondSessionEnd = Carbon::create($currentYear, 12, 30);
        }

        return [$firstSessionStart, $firstSessionEnd, $secondSessionStart, $secondSessionEnd];
    }

    public function OneWeekWinner()
    {
        [$firstSessionStart, $firstSessionEnd, $secondSessionStart, $secondSessionEnd] = $this->GetDateRangeMonth();

        Log::info("Retrieving played data between $firstSessionStart and $secondSessionEnd");

        $userId = Auth::id();

        // Retrieve played data within the specified date range
        $results = LotteryThreeDigitPivot::with('user')
            ->where('user_id', $userId)
            ->where('prize_sent', true)
            ->orderBy('id', 'desc')
            //->whereBetween('created_at', [$firstSessionStart, $firstSessionEnd])
            //->orWhereBetween('created_at', [$secondSessionStart, $secondSessionEnd])
            ->get();

        // Calculate the total sub_amount
        $totalSubAmount = $results->sum(function ($item) {
            return $item->sub_amount;
        });

        // Calculate the total prize amount based on a multiplier
        $totalPrizeAmount = 0;
        foreach ($results as $result) {
            $prizeAmount = $result->sub_amount * 700; // Assuming a multiplier
            $result->prize_amount = $prizeAmount; // Add prize amount to the result
            $totalPrizeAmount += $prizeAmount; // Accumulate total prize amount
        }

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
            'totalPrizeAmount' => $totalPrizeAmount,
        ];
    }

    //  protected function getDateRangeForMonth()
    // {
    //     $today = Carbon::now();
    //     $currentDay = $today->day;
    //     $currentMonth = $today->month;

    //     if ($currentMonth === 12) {
    //         // December has 1st to 16th and 17th to 30th
    //         if ($currentDay <= 16) {
    //             $start = Carbon::create($today->year, 12, 1);
    //             $end = Carbon::create($today->year, 12, 16);
    //         } else {
    //             $start = Carbon::create($today->year, 12, 17);
    //             $end = Carbon::create($today->year, 12, 30);
    //         }
    //     } else {
    //         // Other months have 1st to 16th and 17th to 1st of the next month
    //         if ($currentDay <= 16) {
    //             $start = Carbon::create($today->year, $currentMonth, 1);
    //             $end = Carbon::create($today->year, $currentMonth, 16);
    //         } else {
    //             $start = Carbon::create($today->year, $currentMonth, 17);
    //             $end = Carbon::create($today->year, $currentMonth, 1)->addMonth();
    //         }
    //     }

    //     return [$start, $end];
    // }

    // /**
    //  * Retrieve user played data within the given date range.
    //  *
    //  * @return array
    //  */
    // public function getUserData()
    // {
    //     $userId = Auth::id(); // Authenticated user's ID
    //     [$startDate, $endDate] = $this->getDateRangeForMonth(); // Get the date range

    //     Log::info("Retrieving data for user ID: $userId, from $startDate to $endDate");

    //     // Retrieve lottos for the user in the given date range
    //     $lottos = Lotto::where('user_id', $userId)
    //         ->whereBetween('created_at', [$startDate, $endDate])
    //         ->with('user') // Eager load user details
    //         ->get();

    //     if ($lottos->isEmpty()) {
    //         Log::info("No lottos found for user ID: $userId in the given date range.");

    //         return [
    //             'results' => collect([]),
    //             'totalSubAmount' => 0,
    //         ];
    //     }

    //     // Retrieve the user's played data within the specified date range
    //     $results = DB::table('lotto_three_digit_pivot')
    //         ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
    //         ->join('users', 'lottos.user_id', '=', 'users.id')
    //         ->select(
    //             'users.name as user_name',
    //             'users.phone as user_phone',
    //             'lotto_three_digit_pivot.bet_digit',
    //             'lotto_three_digit_pivot.res_date',
    //             'lotto_three_digit_pivot.res_time',
    //             'lotto_three_digit_pivot.sub_amount',
    //             'lotto_three_digit_pivot.prize_sent',
    //             'lotto_three_digit_pivot.match_status',
    //             'lotto_three_digit_pivot.match_start_date'

    //         )
    //         ->where('lotto_three_digit_pivot.prize_sent', true)
    //         ->whereIn('lotto_three_digit_pivot.lotto_id', $lottos->pluck('id')) // Use lottery IDs
    //         ->get();

    //     Log::info("Retrieved results for user ID: $userId, results count: ".$results->count());

    //     // Calculate the total sub_amount for this user within the relevant date range
    //     $totalSubAmount = DB::table('lotto_three_digit_pivot')
    //         ->whereIn('lotto_three_digit_pivot.lotto_id', $lottos->pluck('id')) // Use lottery IDs
    //         ->whereBetween('lotto_three_digit_pivot.created_at', [$startDate, $endDate])
    //         ->sum('sub_amount');
    //     $totalPrizeAmount = 0;
    //     foreach ($results as $result) {
    //         $prizeAmount = $result->sub_amount * 600; // Prize multiplier
    //         $result->prize_amount = $prizeAmount; // Add prize_amount to each result
    //         $totalPrizeAmount += $prizeAmount; // Accumulate total prize amount
    //     }

    //     Log::info("Total sub_amount for user ID: $userId is $totalSubAmount");

    //     return [
    //         'results' => $results,
    //         'totalSubAmount' => $totalSubAmount,
    //         'totalPrizeAmount' => $totalPrizeAmount,
    //     ];
    // }
}