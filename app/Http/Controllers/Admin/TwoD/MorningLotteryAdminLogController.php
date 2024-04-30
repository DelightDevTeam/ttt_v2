<?php

namespace App\Http\Controllers\Admin\TwoD;

use App\Http\Controllers\Controller;
use App\Services\MorningLotteryAdminLogService;

class MorningLotteryAdminLogController extends Controller
{
    protected $lotteryService;

    public function __construct(MorningLotteryAdminLogService $lotteryService)
    {
        $this->lotteryService = $lotteryService;
    }

    public function MorningAdminLogOpenData()
    {
        // Get data from the service
        $data = $this->lotteryService->getLotteryAdminLog();

        // Return the view with the retrieved data
        return view('admin.two_d.twod_results.morning_rec', [
            'results' => $data['results'],
            'totalSubAmount' => $data['totalSubAmount'],
        ]);
    }
}
