<?php

namespace App\Http\Controllers\Admin\ThreeD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FirstAllWinnerService;
use App\Services\ThirdAllWinnerService;
use App\Services\SecondAllwinnerServices;

class AllWinnerHistoryController extends Controller
{
    protected $lottoService;

    protected $secondWinnerService;

    protected $thirdWinnerService;

    public function __construct(FirstAllWinnerService $lottoService, SecondAllwinnerServices $secondWinnerService, ThirdAllWinnerService $thirdWinnerService)
    {
        $this->lottoService = $lottoService;
        $this->secondWinnerService = $secondWinnerService;
        $this->thirdWinnerService = $thirdWinnerService;

    }

    public function ThreeDFirstWinner()
    {
        $data = $this->lottoService->FirstAllWinner();

        return view('admin.three_d.winners.first_all_prize', compact('data'));
    }

    public function ThreeDSecondWinner()
    {
        $data = $this->secondWinnerService->SecondAllWinner();

        return view('admin.three_d.winners.second_all_prize', compact('data'));
    }

    public function ThreeDThirdWinner()
    {
        $data = $this->thirdWinnerService->ThirdWinner();

        return view('admin.three_d.winners.third_all_prize', compact('data'));
    }
    
   
}
