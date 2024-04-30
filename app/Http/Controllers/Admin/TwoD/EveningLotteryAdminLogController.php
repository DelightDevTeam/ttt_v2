<?php

namespace App\Http\Controllers\Admin\TwoD;

use App\Http\Controllers\Controller;
use App\Services\EveningLotteryAdminLogService;

class EveningLotteryAdminLogController extends Controller
{
    protected $lotteryService;

    public function __construct(EveningLotteryAdminLogService $lotteryService)
    {
        $this->lotteryService = $lotteryService;
    }

    public function showAdminLogOpenData()
    {
        // Get data from the service
        $data = $this->lotteryService->getLotteryAdminLog();

        // Return the view with the retrieved data
        return view('admin.two_d.twod_results.evening_rec', [
            'results' => $data['results'],
            'totalSubAmount' => $data['totalSubAmount'],
        ]);
    }
}
