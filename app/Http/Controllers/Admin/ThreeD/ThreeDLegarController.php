<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\NewThreeDLegarService;

class ThreeDLegarController extends Controller
{
    protected $lotteryService;

    public function __construct(NewThreeDLegarService $lotteryService)
    {
        $this->lotteryService = $lotteryService;
    }

    public function showData()
    {
        $sessionsData = $this->lotteryService->getThreeDigitsData() ?? [];
        return view('admin.three_d.legar.lejar', [
            'data' => $sessionsData,
        ]);
    }
}
