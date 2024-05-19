<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        $currentYear = Carbon::now()->year;

        $currentSession = LotteryThreeDigitPivot::whereYear('match_start_date', $currentYear)
            ->whereMonth('match_start_date', $currentMonth)
            ->first();

        if (!$currentSession) {
            // Handle the case where no session is found
            return [];
        }

        $start = $currentSession->match_start_date;
        $end = $currentSession->res_date;

        $threeDigits = ThreeDigit::all();
        $data = [];

        foreach ($threeDigits as $digit) {
            $show_digit = $digit->id;
            $display = $show_digit - 1;

            // Query for the session data
            $sessionData = DB::table('lotto_three_digit_pivot')
                ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
                ->where('three_digit_id', $display)
                ->whereBetween('lotto_three_digit_pivot.created_at', [$start, $end])
                ->select(
                    'lotto_three_digit_pivot.three_digit_id',
                    DB::raw('SUM(lotto_three_digit_pivot.sub_amount) as total_sub_amount'),
                    DB::raw('GROUP_CONCAT(DISTINCT lotto_three_digit_pivot.bet_digit) as bet_digits'),
                    DB::raw('COUNT(*) as total_bets'),
                    DB::raw('MAX(lotto_three_digit_pivot.created_at) as latest_bet_time')
                )
                ->groupBy('lotto_three_digit_pivot.three_digit_id')
                ->first();

            $data[$digit->three_digit] = [
                'three_digit_id' => $display,
                'total_sub_amount' => $sessionData->total_sub_amount ?? 0,
                'bet_digits' => $sessionData->bet_digits ?? '',
                'total_bets' => $sessionData->total_bets ?? 0,
                'latest_bet_time' => $sessionData->latest_bet_time ?? null,
            ];
        }

        return $data;
    }
}
