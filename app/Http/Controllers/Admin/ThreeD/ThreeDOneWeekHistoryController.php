<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\ThreeDOneWeekHistoryService;

class ThreeDOneWeekHistoryController extends Controller
{
    protected $userLotteryDataService;

    public function __construct(ThreeDOneWeekHistoryService $userLotteryDataService)
    {
        $this->userLotteryDataService = $userLotteryDataService;
    }

    public function GetAllThreeDUserData()
    {
        try {
            // Retrieve data for all users
            $data = $this->userLotteryDataService->getAllUserData();

            // Pass the results to the Blade view
            return view('admin.three_d.three_d_history', [
                'results' => $data['results'],
                'totalSubAmount' => $data['totalSubAmount'],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to retrieve data: '.$e->getMessage());
        }
    }
}
