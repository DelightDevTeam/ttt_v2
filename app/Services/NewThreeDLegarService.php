<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ThreeDigit\ThreeDigit;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;

class NewThreeDLegarService
{
    /**
     * Get data for three-digit entries within the current month's date range.
     *
     * @return array
     */
    public function getThreeDigitsData()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $currentDay = Carbon::now()->day;

        Log::info('current month is: ' . $currentMonth);
        Log::info('current year is: ' . $currentYear);

        $currentSession = LotteryThreeDigitPivot::whereYear('res_date', $currentYear)
            ->whereMonth('res_date', $currentMonth)
            ->first();

        if (!$currentSession) {
            // Handle the case where no session is found
            Log::warning('No session found for the current month.');
            return [];
        }

        // Determine the current match time based on the current date
        $currentMatchTime = null;
        if ($currentDay <= 1) {
            $currentMatchTime = $currentSession;
        } elseif ($currentDay > 1 && $currentDay <= 16) {
            $currentMatchTime = LotteryThreeDigitPivot::whereYear('res_date', $currentYear)
                ->whereMonth('res_date', $currentMonth)
                ->skip(1)
                ->first();
        } else {
            $nextMonthMatchTimes = LotteryThreeDigitPivot::whereYear('res_date', $currentYear)
                ->whereMonth('res_date', $currentMonth + 1)
                ->orderBy('res_date', 'asc')
                ->first();

            $currentMatchTime = $nextMonthMatchTimes;
        }

        $firstSessionStart = $currentSession->match_start_date;
        Log::info('first session start date is: ' . $firstSessionStart);

        $firstSessionEnd = $currentSession->res_date;
        Log::info('first session end date is: ' . $firstSessionEnd);

        $threeDigits = ThreeDigit::all();
        $data = [];

        foreach ($threeDigits as $digit) {
            $show_digit = $digit->id;
            $display = $show_digit - 1;

            // Query for the first session data
            $firstSessionData = LotteryThreeDigitPivot::join('result_dates', function ($join) {
                $join->on('lotto_three_digit_pivot.match_start_date', '=', 'result_dates.match_start_date')
                    ->where('result_dates.status', '=', 'open');
            })
                ->where('lotto_three_digit_pivot.three_digit_id', $display)
                ->select(
                    'lotto_three_digit_pivot.three_digit_id',
                    DB::raw('SUM(lotto_three_digit_pivot.sub_amount) as total_sub_amount'),
                    DB::raw('GROUP_CONCAT(DISTINCT lotto_three_digit_pivot.bet_digit) as bet_digits'),
                    DB::raw('COUNT(*) as total_bets'),
                    DB::raw('MAX(lotto_three_digit_pivot.created_at) as latest_bet_time')
                )
                ->groupBy('lotto_three_digit_pivot.three_digit_id')
                ->first();

            // Combine the results
            $combinedData = [
                'three_digit_id' => $display,
                'total_sub_amount' => $firstSessionData->total_sub_amount ?? 0,
                'bet_digits' => $firstSessionData->bet_digits ?? '',
                'total_bets' => $firstSessionData->total_bets ?? 0,
                'latest_bet_time' => $firstSessionData->latest_bet_time ?? null,
            ];

            $data[$digit->three_digit] = $combinedData;
        }

        return $data;
    }
}
