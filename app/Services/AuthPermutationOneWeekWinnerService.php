<?php

namespace App\Services;

use App\Models\Lotto;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// ... Other imports ...

class AuthPermutationOneWeekWinnerService
{
    protected function GetDateRangeMonth()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Define the date range for the sessions
        $firstSessionStart = Carbon::create($currentYear, $currentMonth, 3);
        $firstSessionEnd = Carbon::create($currentYear, $currentMonth, 16);

        $secondSessionStart = Carbon::create($currentYear, $currentMonth, 17);
        $secondSessionEnd = Carbon::create($currentYear, $currentMonth + 1, 1);

        // Special case for May
        if ($currentMonth == 5) {
            $firstSessionStart = Carbon::create($currentYear, 5, 3); // 17th of April (previous month)
            $firstSessionEnd = Carbon::create($currentYear, 5, 16);    // 2nd of May

            $secondSessionStart = Carbon::create($currentYear, 5, 17); // 17th of May
            $secondSessionEnd = Carbon::create($currentYear, 6, 1);    // 1st of June
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

    public function OneWeekPermutationWinner()
    {
        [$firstSessionStart, $firstSessionEnd, $secondSessionStart, $secondSessionEnd] = $this->GetDateRangeMonth();

        Log::info("Retrieving played data between $firstSessionStart and $secondSessionEnd");

        $userId = Auth::id();

        // Retrieve played data within the specified date range
        $results = LotteryThreeDigitPivot::with('user')
            ->where('user_id', $userId)
            ->where('prize_sent', 2)
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
            $prizeAmount = $result->sub_amount * 10; // Assuming a multiplier
            $result->prize_amount = $prizeAmount; // Add prize amount to the result
            $totalPrizeAmount += $prizeAmount; // Accumulate total prize amount
        }

        return [
            'results' => $results,
            'totalSubAmount' => $totalSubAmount,
            'totalPrizeAmount' => $totalPrizeAmount,
        ];
    }
}
