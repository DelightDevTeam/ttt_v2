<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\Lotto;
use App\Models\ThreeDigit\ThreeWinner;
use App\Services\ThreeDAllUserDataService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ThreeDListController extends Controller
{
    protected $userLotteryDataService;

    public function __construct(ThreeDAllUserDataService $userLotteryDataService)
    {
        $this->userLotteryDataService = $userLotteryDataService;
    }

    public function GetAllThreeDData()
    {
        try {
            // Retrieve data for all users
            $data = $this->userLotteryDataService->getAllThreeData();

            // Pass the results to the Blade view
            return view('admin.three_d.three_d_list_index', [
                'results' => $data['results'],
                'totalSubAmount' => $data['totalSubAmount'],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to retrieve data: '.$e->getMessage());
        }
    }

    public function show(string $id)
    {
        $lottery = Lotto::with('threedDigits')->findOrFail($id);
        $prize_no = ThreeWinner::whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->first();
        $today = Carbon::now();
        if ($today->day <= 1) {
            $targetDay = 1;
        } else {
            $targetDay = 16;
            // If today is after the 16th, then target the 1st of next month
            if ($today->day > 16) {
                $today->addMonthNoOverflow();
                $today->day = 1;
            }
        }
        $matchTime = DB::table('threed_match_times')
            ->whereMonth('match_time', '=', $today->month)
            ->whereYear('match_time', '=', $today->year)
            ->whereDay('match_time', '=', $targetDay)
            ->first();

        return view('admin.three_d.three_d_list_show', compact('lottery', 'prize_no', 'matchTime'));
    }
}
