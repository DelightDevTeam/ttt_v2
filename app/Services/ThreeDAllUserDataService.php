<?php

namespace App\Services;

use App\Models\ThreeDigit\Lotto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThreeDAllUserDataService
{
    /**
     * Retrieve all user play data.
     *
     * @return array
     */
    public function getAllThreeData()
    {
        Log::info('Retrieving all user play data.');

        // Retrieve all lottos, regardless of date range
        $lottos = Lotto::with('user')->get(); // Eager load user details

        if ($lottos->isEmpty()) {
            Log::info('No lottos found.');

            return [
                'results' => collect([]), // Return an empty collection if no data
                'totalSubAmount' => 0, // Default total sub_amount to zero
            ];
        }

        // Retrieve all user play data
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
            ->get();

        Log::info('Retrieved results count: '.$results->count());

        // Calculate the total sub_amount for all users
        $totalSubAmount = DB::table('lotto_three_digit_pivot')->sum('sub_amount');

        Log::info("Total sub_amount for all user play data is $totalSubAmount");

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
        ];
    }
}
