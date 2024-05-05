<?php

namespace App\Http\Controllers\User\WinHistory;

use App\Http\Controllers\Controller;
use App\Services\TwodAllPrizeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TwoDWinnerHistoryController extends Controller
{
    protected $prizeSentService;

    public function __construct(TwodAllPrizeService $prizeSentService)
    {
        $this->prizeSentService = $prizeSentService;
    }

    public function winnerHistory()
    {
        try {
            $data = $this->prizeSentService->AllWinPrizeSentForAdmin();

            // Always ensure data is returned to the view
            return view('two_d.win_history.index', [
                'results' => $data['results'],
                'totalPrizeAmount' => $data['totalPrizeAmount'],
            ]);

        } catch (Exception $e) {
            return view('two_d.win_history.index', [
                'results' => collect([]), // Default to empty collection
                'totalPrizeAmount' => 0,
                'error' => 'Failed to retrieve data. Please try again later.',
            ]);
        }
    }
}
