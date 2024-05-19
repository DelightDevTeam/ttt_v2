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
        $currentMonth = Carbon::now()->month;
        Log::info('current month is: ' . $currentMonth);
        $currentYear = Carbon::now()->year;
        Log::info('current year is: ' . $currentYear);

        $currentSession = LotteryThreeDigitPivot::whereYear('match_start_date', $currentYear)
            ->whereMonth('match_start_date', $currentMonth)
            ->first();

        if (!$currentSession) {
            // Handle the case where no session is found
            return [];
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

            
            // Combine the results from both sessions (if needed)
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
