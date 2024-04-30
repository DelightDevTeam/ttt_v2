<?php

namespace App\Http\Controllers\Admin\TwoD;

use App\Http\Controllers\Controller;
use App\Models\Two\TwodGameResult;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
    public function index()
    {
        // Get today's date
        $today = Carbon::now()->format('Y-m-d');

        // Determine the current session
        $currentSession = $this->getCurrentSession();

        if ($currentSession === 'closed') {
            return view('admin.two_d.twod_results.index', ['results' => 'Session is closed']);
        }

        // Retrieve the data for the current day and session
        $result = TwodGameResult::where('result_date', $today)
            ->where('session', $currentSession) // Ensure correct session
            ->first();

        return view('admin.two_d.twod_results.index', ['result' => $result]);
    }

    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status'); // The new status

        // Find the result by ID
        $result = TwodGameResult::findOrFail($id);

        // Update the status
        $result->status = $status;
        $result->save();

        // Return a response (like a JSON object)
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }

    public function updateResultNumber(Request $request, $id)
    {
        $result_number = $request->input('result_number'); // The new status

        // Find the result by ID
        $result = TwodGameResult::findOrFail($id);

        // Update the status
        $result->result_number = $result_number;
        $result->save();

        // Return a response (like a JSON object)
        return redirect()->back()->with('success', 'Result number updated successfully.'); // Redirect back with success message
    }
}
