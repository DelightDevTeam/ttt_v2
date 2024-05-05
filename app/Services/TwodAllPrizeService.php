<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwodAllPrizeService
{
    public function AllWinPrizeSentForAdmin()
    {
        try {
            Log::info('Entering AllWinPrizeSentForAdmin');

            // Define the start of the time range (1 month ago)
            $oneMonthAgo = Carbon::now()->subMonth()->startOfDay();
            Log::info('Start of date range:', ['date' => $oneMonthAgo]);

            // Define the end of the current month
            $endOfCurrentMonth = Carbon::now()->endOfMonth();
            Log::info('End of date range:', ['date' => $endOfCurrentMonth]);

            // Define session time ranges
            $morningStart = '04:01:00';
            $morningEnd = '12:10:00';
            $eveningStart = '12:05:00';
            $eveningEnd = '16:45:00';

            Log::info('Session times:', [
                'morningStart' => $morningStart,
                'morningEnd' => $morningEnd,
                'eveningStart' => $eveningStart,
                'eveningEnd' => $eveningEnd,
            ]);

            // Retrieve results with adjusted conditions
            $results = DB::table('lottery_two_digit_pivot')
                ->join('users', 'lottery_two_digit_pivot.user_id', '=', 'users.id')
                ->select(
                    'users.name as user_name',
                    'users.phone as user_phone',
                    'users.profile as user_profile',
                    'lottery_two_digit_pivot.bet_digit',
                    'lottery_two_digit_pivot.res_date',
                    'lottery_two_digit_pivot.sub_amount',
                    'lottery_two_digit_pivot.session',
                    'lottery_two_digit_pivot.res_time',
                    'lottery_two_digit_pivot.prize_sent'
                )
                // Only include prize-sent records
                ->where('lottery_two_digit_pivot.prize_sent', true)
                // Consider only weekdays (Monday to Friday)
                ->whereRaw('WEEKDAY(lottery_two_digit_pivot.res_date) BETWEEN 0 AND 4')
                // Consider the defined date range
                ->whereBetween('lottery_two_digit_pivot.res_date', [$oneMonthAgo, $endOfCurrentMonth])
                // Consider session time ranges
                ->where(function ($query) use ($morningStart, $morningEnd, $eveningStart, $eveningEnd) {
                    $query->whereBetween('lottery_two_digit_pivot.res_time', [$morningStart, $morningEnd])
                        ->orWhereBetween('lottery_two_digit_pivot.res_time', [$eveningStart, $eveningEnd]);
                })
                ->get();

            // Log the query results
            Log::info('Query results:', ['results' => $results]);

            // Calculate the total prize amount
            $totalPrizeAmount = 0;
            foreach ($results as $result) {
                $prizeAmount = $result->sub_amount * 85; // Prize multiplier
                $totalPrizeAmount += $prizeAmount;
            }

            Log::info('Total prize amount:', ['amount' => $totalPrizeAmount]);

            Log::info('Exiting AllWinPrizeSentForAdmin successfully');

            return ['results' => $results, 'totalPrizeAmount' => $totalPrizeAmount];

        } catch (\Exception $e) {
            Log::error('Error retrieving prize_sent data: '.$e->getMessage());

            return ['results' => collect([]), 'totalPrizeAmount' => 0];
        }
    }
}
