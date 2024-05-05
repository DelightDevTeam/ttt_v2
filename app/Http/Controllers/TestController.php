<?php

namespace App\Http\Controllers;

use App\Models\ThreeDigit\ResultDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function index()
    {
        // Get the current year and month
        //     $currentYear = Carbon::now()->year;
        //     $currentMonth = Carbon::now()->month;

        //     // Query ResultDate to get all records for the current month
        //     $resultDates = ResultDate::whereYear('result_date', $currentYear)
        //                             ->whereMonth('result_date', $currentMonth)
        //                             ->get();

        //     // Optionally, log the result dates for debugging
        //     Log::info("Result dates for the current month:", ['resultDates' => $resultDates]);

        //     // Use the result dates in your application logic
        //     foreach ($resultDates as $resultDate) {
        //         echo "Result Date: " . $resultDate->result_date . "\n";
        //         echo "Match Start Date: " . $resultDate->match_start_date . "\n";
        //     }

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Get all result dates for the current month
        $currentMonthResultDates = ResultDate::whereYear('result_date', $currentYear)
            ->whereMonth('result_date', $currentMonth)
            ->get();

        // Get the first result date of the next month
        $nextMonth = ($currentMonth % 12) + 1; // Calculate the next month (1-12, looping back to 1 after 12)
        $nextMonthYear = $nextMonth == 1 ? $currentYear + 1 : $currentYear; // Increment year if it's January

        $firstResultDateNextMonth = ResultDate::whereYear('result_date', $nextMonthYear)
            ->whereMonth('result_date', $nextMonth)
            ->orderBy('result_date', 'asc')
            ->first(); // Get the first result date of the next month

        // Merge the results into one collection
        $allResultDates = $currentMonthResultDates->merge(collect([$firstResultDateNextMonth]));

        // Log the result dates for debugging
        Log::info('Result dates including the current month and the first game of the next month:', ['resultDates' => $allResultDates]);

        // Use the combined result dates
        foreach ($allResultDates as $resultDate) {
            if ($resultDate) { // Check if the resultDate object is not null
                echo 'Result Date: '.$resultDate->result_date."\n";
                echo 'Match Start Date: '.$resultDate->match_start_date."\n";
            }
        }

    }
}
