<?php

namespace App\Http\Controllers\Admin\TwoD;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\SessionHelper;
use App\Models\TwoD\TwodGameResult;
use App\Http\Controllers\Controller;
use App\Models\TwoD\LotteryTwoDigitPivot;

class TwoGameResultController extends Controller
{
    protected function getCurrentSession()
    {
        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:00:00' && $currentTime <= '12:01:00') {
            return 'morning';
        } elseif ($currentTime >= '12:01:01' && $currentTime <= '16:30:00') {
            return 'evening';
        } else {
            return 'closed'; // If outside known session times
        }
    }

    /**
     * Display the current session's data for the day.
     */
    // public function index()
    // {
    //     // Get today's date
    //     $today = Carbon::now()->format('Y-m-d');

    //     // Retrieve the latest result for today's morning session
    //     $morningResult = TwodGameResult::where('result_date', $today)
    //         ->where('session', 'morning') // Check for morning session
    //         //->orderBy('created_at', 'desc') // Get the latest record by creation time
    //         ->first();

    //     // Retrieve the latest result for today's evening session
    //     $eveningResult = TwodGameResult::where('result_date', $today)
    //         ->where('session', 'evening') // Check for evening session
    //         //->orderBy('created_at', 'desc') // Get the latest record by creation time
    //         ->first();

    //     return view('admin.two_d.twod_results.index', [
    //         'morningResult' => $morningResult,
    //         'eveningResult' => $eveningResult,
    //     ]);
    // }

    public function index()
    {
        // Get today's date
        $today = Carbon::now()->format('Y-m-d');
        //dd($today);
        // Retrieve the latest result for today's morning session
        $morningSession = TwodGameResult::where('result_date', $today)
            ->where('session', 'morning')
            ->first();
        //dd($morningSession);
        // Retrieve the latest result for today's evening session
        $eveningSession = TwodGameResult::where('result_date', $today)
            ->where('session', 'evening')
            ->first();

        return view('admin.two_d.twod_results.index', [
            'morningSession' => $morningSession,
            'eveningSession' => $eveningSession,
        ]);
    }


     public function getCurrentMonthResults()
    {
        // Get the start and end of the current month
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Retrieve all records within the current month
        $results = TwodGameResult::whereBetween('result_date', [$currentMonthStart, $currentMonthEnd])
            ->orderBy('result_date', 'asc') // Optional: order by date
            ->get();

        // Return the data to the view or as a JSON response
        return view('admin.two_d.twod_results.current_month_index', ['results' => $results]);
    }

    // public function updateStatus(Request $request, $id)
    // {
    //     $status = $request->input('status'); // The new status

    //     // Find the result by ID
    //     $result = TwodGameResult::findOrFail($id);

    //     // Update the status
    //     $result->status = $status;
    //     $result->save();

    //     // Return a response (like a JSON object)
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Status updated successfully.',
    //     ]);
    // }

    public function updateStatus(Request $request, $id)
    {
        //dd($request->all());
        $status = $request->input('status'); // The new status
        //dd($status);
        // Find the result by ID
        $result = TwodGameResult::findOrFail($id);

        // Update the status
        $result->status = $status;
        $result->save();
        session()->flash('SuccessRequest', '2D Open/Close Status updated successfully');

        return redirect()->back()->with('success', '2D Open/Close Status updated successfully.'); // Redirect back with success message

    }

    public function updateStatusEvening(Request $request, $id)
    {
        //dd($request->all());
        $status = $request->input('status'); // The new status
        //dd($status);
        // Find the result by ID
        $result = TwodGameResult::findOrFail($id);

        // Update the status
        $result->status = $status;
        $result->save();
        session()->flash('SuccessRequestEvening', '2D Open/Close Status updated successfully');

        return redirect()->back()->with('success', '2D Open/Close Status updated successfully.'); // Redirect back with success message

    }


    // public function updateStatus(Request $request, $id)
    // {
    //     // Get the new status with a fallback default
    //     $newStatus = $request->input('status', 'closed'); // Default to 'closed' if not provided

    //     // Find the existing record and update the status
    //     $result = TwodGameResult::findOrFail($id);

    //     // Ensure the status is not NULL before updating
    //     if (is_null($newStatus)) {
    //         return redirect()->back()->with('error', 'Status cannot be null');
    //     }

    //     $result->status = $newStatus;
    //     $result->save();

    //     return redirect()->back()->with('success', "Status changed to '{$newStatus}' successfully.");
    // }

    public function updateResultNumber(Request $request, $id)
    {
        $result_number = $request->input('result_number'); // The new status

        // Find the result by ID
        $result = TwodGameResult::findOrFail($id);

        // Update the status
        $result->result_number = $result_number;
        $result->save();

        $today = Carbon::today();
        $session = SessionHelper::getCurrentSession();
        $twod_data = LotteryTwoDigitPivot::where('res_date', $today)
            ->where('session', $session)->get();
        foreach ($twod_data as $twod) {
            $twod->update(['win_lose' => 1]);
        }

        // Return a response (like a JSON object)
        return redirect()->back()->with('success', 'Result number updated successfully.'); // Redirect back with success message
    }
}