<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Permutation;
use App\Models\ThreeDigit\Prize;
use App\Models\ThreeDigit\ResultDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResultDateController extends Controller
{
    public function index()
    {
        // Get the current year and month
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Get result dates for the current month
        $currentMonthResultDates = ResultDate::whereYear('result_date', $currentYear)
            ->whereMonth('result_date', $currentMonth)
            ->get();

        // Determine the next month and year
        $nextMonth = ($currentMonth % 12) + 1; // Calculate the next month (1-12, looping back to 1 after 12)
        $nextMonthYear = ($nextMonth == 1) ? $currentYear + 1 : $currentYear; // Increment year if it's January

        // Get the first result date of the next month
        $firstResultDateNextMonth = ResultDate::whereYear('result_date', $nextMonthYear)
            ->whereMonth('result_date', $nextMonth)
            ->orderBy('result_date', 'asc')
            ->first();

        // Merge the current month results with the first result date of the next month
        $results = $currentMonthResultDates->merge(collect([$firstResultDateNextMonth]));

        // Log the result dates for debugging
        //Log::info('Result dates including the current month and the first game of the next month:', ['resultDates' => $results]);

        //Get the latest prize where status is open
        $lasted_prizes = ResultDate::where('status', 'open')
            ->orderBy('result_date', 'desc') // Ensure to get the latest result date
            ->first();
        // Retrieve permutation digits and the latest prize
        $permutation_digits = Permutation::all();
        $three_digits_prize = Prize::orderBy('id', 'desc')->first();

        // Return the view with the required data
        return view('admin.three_d.result_date.index', compact('results', 'lasted_prizes', 'permutation_digits', 'three_digits_prize'));
    }

    public function getCurrentMonthResultsSetting()
    {
         $currentMonthStart = Carbon::now()->startOfMonth();

        // Get the end of the next month
        $nextMonthEnd = Carbon::now()->addMonth()->endOfMonth();

        // Retrieve all records within the current month and next month
        $results = ResultDate::whereBetween('result_date', [$currentMonthStart, $nextMonthEnd])
            ->orderBy('result_date', 'asc') // Optional: order by date
            ->get();

        // Return the data to the view or as a JSON response
        return view('admin.three_d.result_date.current_month_index', ['results' => $results]);
    }
    // public function index()
    // {
    //     // Get the start and end dates for the current month
    //     $currentMonthStart = Carbon::now()->startOfMonth();
    //     $currentMonthEnd = Carbon::now()->endOfMonth();

    //     // Get the start and end dates for the next month
    //     $nextMonthStart = Carbon::now()->addMonth()->startOfMonth();
    //     $nextMonthEnd = Carbon::now()->addMonth()->endOfMonth();

    //     // Fetch results with status 'open' or 'closed' within these date ranges
    //     $results = ResultDate::whereIn('status', ['open', 'closed'])
    //         ->where(function ($query) use ($currentMonthStart, $currentMonthEnd, $nextMonthStart, $nextMonthEnd) {
    //             $query->whereBetween('result_date', [$currentMonthStart, $currentMonthEnd])
    //                 ->orWhereBetween('result_date', [$nextMonthStart, $nextMonthEnd]);
    //         })
    //         ->get();
    //     $lasted_prizes = ResultDate::where('status', 'open')
    //         ->where(function ($query) use ($currentMonthStart, $currentMonthEnd, $nextMonthStart, $nextMonthEnd) {
    //             $query->whereBetween('result_date', [$currentMonthStart, $currentMonthEnd])
    //                 ->orWhereBetween('result_date', [$nextMonthStart, $nextMonthEnd]);
    //         })
    //         ->orderBy('result_date', 'desc') // Ensure to get the latest result date
    //         ->first();
    //     $permutation_digits = Permutation::all();

    //     $three_digits_prize = Prize::orderBy('id', 'desc')->first();

    //     return view('admin.three_d.result_date.index', compact('results', 'lasted_prizes', 'permutation_digits', 'three_digits_prize'));
    // }

    public function updateStatus(Request $request, $id)
    {
        // Get the new status with a fallback default
        $newStatus = $request->input('status', 'closed'); // Default to 'closed' if not provided

        // Find the existing record and update the status
        $result = ResultDate::findOrFail($id);

        // Ensure the status is not NULL before updating
        if (is_null($newStatus)) {
            return redirect()->back()->with('error', 'Status cannot be null');
        }

        $result->status = $newStatus;
        $result->save();

        return redirect()->back()->with('success', "Status changed to '{$newStatus}' successfully.");
    }

    public function AdminLogThreeDOpenClose(Request $request, $id)
    {
        // Get the new status with a fallback default
        $newStatus = $request->input('admin_log', 'closed'); // Default to 'closed' if not provided

        // Find the existing record and update the status
        $result = LotteryThreeDigitPivot::findOrFail($id);

        // Ensure the status is not NULL before updating
        if (is_null($newStatus)) {
            return redirect()->back()->with('error', 'Admin Log cannot be null');
        }

        $result->admin_log = $newStatus;
        $result->save();

        return redirect()->back()->with('success', "Admin Log changed to '{$newStatus}' successfully.");
    }

    public function UserLogThreeDOpenClose(Request $request, $id)
    {
        // Get the new status with a fallback default
        $newStatus = $request->input('user_log', 'closed'); // Default to 'closed' if not provided

        // Find the existing record and update the status
        $result = ResultDate::findOrFail($id);

        // Ensure the status is not NULL before updating
        if (is_null($newStatus)) {
            return redirect()->back()->with('error', 'User Log cannot be null');
        }

        $result->user_log = $newStatus;
        $result->save();

        return redirect()->back()->with('success', "User Log changed to '{$newStatus}' successfully.");
    }

    // public function updateStatus(Request $request, $id)
    // {
    //       $newStatus = $request->input('status');

    //         // Find the existing record and update the status
    //         $result = ResultDate::findOrFail($id);
    //         $result->status = $newStatus;
    //         $result->save();

    //         return redirect()->back()->with('success', "Status changed to '{$newStatus}' successfully.");
    //     // $status = $request->input('status'); // The new status

    //     // // Find the result by ID
    //     // $result = ResultDate::findOrFail($id);

    //     // // Update the status
    //     // $result->status = $status;
    //     // $result->save();

    //     // // Return a response (like a JSON object)
    //     // return response()->json([
    //     //     'success' => true,
    //     //     'message' => 'Status updated successfully.',
    //     // ]);
    // }
    public function updateResultNumber(Request $request, $id)
    {
        $result_number = $request->input('result_number'); // The new status

        // Find the result by ID
        $result = ResultDate::findOrFail($id);

        // Update the status
        $result->result_number = $result_number;
        $result->save();

        $draw_date = ResultDate::where('status', 'open')->first();
        $start_date = $draw_date->match_start_date;
        $end_date = $draw_date->result_date;
        $today = Carbon::today();

        $three_digits = LotteryThreeDigitPivot::whereBetween('match_start_date', [$start_date, $end_date])
        ->whereBetween('res_date', [$start_date, $end_date])
        ->get();
        foreach ($three_digits as $digit) {
            $digit->update(['win_lose' => 1]);
        }

        // Return a response (like a JSON object)
        return redirect()->back()->with('success', 'Result number updated successfully.'); // Redirect back with success message
    }
}