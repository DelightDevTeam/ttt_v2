<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\PermutationOneWeekWinnerService;

class PermutationWinnerController extends Controller
{
    protected $userLotteryDataService;

    public function __construct(PermutationOneWeekWinnerService $userLotteryDataService)
    {
        $this->userLotteryDataService = $userLotteryDataService;
    }

    public function PermutationWinners()
    {
        try {
            // Retrieve data for all users
            $data = $this->userLotteryDataService->OneWeekPermutationWinner();

            // Pass the results to the Blade view
            return view('admin.three_d.three_d_permutation_winner_history', [
                'results' => $data['results'],
                'totalSubAmount' => $data['totalSubAmount'],
                'totalPrizeAmount' => $data['totalPrizeAmount'],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to retrieve data: '.$e->getMessage());
        }
    }
}
