<?php

namespace App\Services;

use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\ResultDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LottoSessionService
{
    /**
     * Retrieve data for the authenticated user within the current month's sessions
     * and calculate the total sub_amount.
     *
     * @return array
     */
    public function getThreeDigitData()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Define the date range for the sessions
        $firstSessionStart = Carbon::create($currentYear, $currentMonth, 1);
        $firstSessionEnd = Carbon::create($currentYear, $currentMonth, 16);

        $secondSessionStart = Carbon::create($currentYear, $currentMonth, 17);
        $secondSessionEnd = Carbon::create($currentYear, $currentMonth + 1, 1); // 1st of the next month

        // Special case for May
        if ($currentMonth == 5) {
            $firstSessionStart = Carbon::create($currentYear, 5, 3); // 17th of the previous month
            $firstSessionEnd = Carbon::create($currentYear, 5, 16);    // 2nd of May

            $secondSessionStart = Carbon::create($currentYear, 5, 17); // 17th of May
            $secondSessionEnd = Carbon::create($currentYear, 6, 1);    // 1st of June
        }

        // Get the authenticated user's ID
        $userId = Auth::id();
        $open_date = ResultDate::where('status', 'open')
            ->get();
        $dates = []; // Initialize an array

        foreach ($open_date as $date) {
            $dates[] = $date->id; // Add each ID to the array
        }

        Log::info('Open result date IDs:', ['dates' => $dates]);

        // Check if the $dates array is empty
        if (empty($dates) || ! is_array($dates)) {
            Log::warning('No open result dates found or $dates is not an array');

            return; // Exit the function if no valid open dates
        }
        // Fetch the data from the LotteryThreeDigitPivot model
        $threeDigitData = LotteryThreeDigitPivot::where('user_id', $userId) // Filter by user ID
            ->whereBetween('res_date', [$firstSessionStart, $firstSessionEnd]) // First session
            ->orWhereBetween('res_date', [$secondSessionStart, $secondSessionEnd]) // Second session
            ->get(); // Fetch the results

        // Calculate the total sub_amount for all retrieved records
        $totalAmount = $threeDigitData->sum(function ($item) {
            return $item->sub_amount; // Sum the sub_amount for each related record
        });

        return [
            'threeDigit' => $threeDigitData, // The fetched records
            'total_amount' => $totalAmount, // The calculated total sub_amount
        ];
    }
}
