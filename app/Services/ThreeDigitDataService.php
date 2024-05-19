<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ThreeDigit\ThreeDigit;

class ThreeDigitDataService
{
    /**
     * Get the date range for the current month.
     *
     * @return array
     */
    protected function getDateRangeMonth()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Default date range for sessions
        $firstSessionStart = Carbon::create($currentYear, $currentMonth, 2);
        $firstSessionEnd = Carbon::create($currentYear, $currentMonth, 16);

        $secondSessionStart = Carbon::create($currentYear, $currentMonth, 17);
        $secondSessionEnd = Carbon::create($currentYear, $currentMonth + 1, 1);

        // Special case for May
        if ($currentMonth == 5) {
            $firstSessionStart = Carbon::create($currentYear, 5, 3);
            $firstSessionEnd = Carbon::create($currentYear, 5, 16);

            $secondSessionStart = Carbon::create($currentYear, 5, 17);
            $secondSessionEnd = Carbon::create($currentYear, 6, 1);
        }

        // Special case for December
        if ($currentMonth == 12) {
            $firstSessionStart = Carbon::create($currentYear, 12, 2);
            $firstSessionEnd = Carbon::create($currentYear, 12, 16);

            $secondSessionStart = Carbon::create($currentYear, 12, 17);
            $secondSessionEnd = Carbon::create($currentYear, 12, 30);
        }

        return [$firstSessionStart, $firstSessionEnd, $secondSessionStart, $secondSessionEnd];
    }

    /**
     * Get data for three-digit entries within the current month's date range.
     *
     * @return array
     */
    public function getThreeDigitsData()
    {
        [$firstSessionStart, $firstSessionEnd, $secondSessionStart, $secondSessionEnd] = $this->getDateRangeMonth();

        $threeDigits = ThreeDigit::all();
        $data = [];

        foreach ($threeDigits as $digit) {
            $show_digit = $digit->id;
            $display = $show_digit - 1;

            // Query for the first session
            $firstSessionData = DB::table('lotto_three_digit_pivot')
                ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
                ->where('three_digit_id', $display)
                ->whereBetween('lotto_three_digit_pivot.created_at', [$firstSessionStart, $firstSessionEnd])
                ->select(
                    'lotto_three_digit_pivot.three_digit_id',
                    DB::raw('SUM(lotto_three_digit_pivot.sub_amount) as total_sub_amount'),
                    DB::raw('GROUP_CONCAT(DISTINCT lotto_three_digit_pivot.bet_digit) as bet_digits'),
                    DB::raw('COUNT(*) as total_bets'),
                    DB::raw('MAX(lotto_three_digit_pivot.created_at) as latest_bet_time')
                )
                ->groupBy('lotto_three_digit_pivot.three_digit_id')
                ->first();

            // Query for the second session
            $secondSessionData = DB::table('lotto_three_digit_pivot')
                ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
                ->where('three_digit_id', $display)
                ->whereBetween('lotto_three_digit_pivot.created_at', [$secondSessionStart, $secondSessionEnd])
                ->select(
                    'lotto_three_digit_pivot.three_digit_id',
                    DB::raw('SUM(lotto_three_digit_pivot.sub_amount) as total_sub_amount'),
                    DB::raw('GROUP_CONCAT(DISTINCT lotto_three_digit_pivot.bet_digit) as bet_digits'),
                    DB::raw('COUNT(*) as total_bets'),
                    DB::raw('MAX(lotto_three_digit_pivot.created_at) as latest_bet_time')
                )
                ->groupBy('lotto_three_digit_pivot.three_digit_id')
                ->first();

            // Combine the results from both sessions
            $combinedData = [
                'three_digit_id' => $display,
                'total_sub_amount' => ($firstSessionData->total_sub_amount ?? 0) + ($secondSessionData->total_sub_amount ?? 0),
                'bet_digits' => implode(',', array_unique(array_filter([
                    $firstSessionData->bet_digits ?? '',
                    $secondSessionData->bet_digits ?? '',
                ]))),
                'total_bets' => ($firstSessionData->total_bets ?? 0) + ($secondSessionData->total_bets ?? 0),
                'latest_bet_time' => max($firstSessionData->latest_bet_time ?? null, $secondSessionData->latest_bet_time ?? null),
            ];

            $data[$digit->three_digit] = $combinedData;
        }

        return $data;
    }
}
