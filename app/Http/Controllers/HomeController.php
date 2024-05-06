<?php

namespace App\Http\Controllers;

use App\Models\Admin\LotteryMatch;
use App\Models\ThreeDigit\Lotto;
use App\Models\TwoD\Lottery;
use App\Models\User;
use App\Services\AuthWinLotteryPrizeService;
use App\Services\EveningLotteryService;
use App\Services\MorningLotteryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $eveningLotteryService;

    protected $morningLotteryService;

    protected $winnerSevice;

    public function __construct(EveningLotteryService $eveningLotteryService, MorningLotteryService $morningLotteryService, AuthWinLotteryPrizeService $winnerSevice)
    {
        $this->middleware('auth');

        $this->eveningLotteryService = $eveningLotteryService;
        $this->morningLotteryService = $morningLotteryService;
        $this->winnerSevice = $winnerSevice;
    }

    public function index()
    {
        $user = Auth::user(); // Get the authenticated user

        if ($user->hasRole('Admin')) {
            // Retrieve total amounts for different time frames
            $today = now()->today();
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            $dailyTotal = Lottery::whereDate('created_at', $today)->sum('total_amount');
            $weeklyTotal = Lottery::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('total_amount');
            $monthlyTotal = Lottery::whereMonth('created_at', '=', now()->month)
                ->whereYear('created_at', '=', now()->year)
                ->sum('total_amount');
            $yearlyTotal = Lottery::whereYear('created_at', '=', now()->year)->sum('total_amount');

            // Totals for 3D lotteries
            $three_d_dailyTotal = Lotto::whereDate('created_at', $today)->sum('total_amount');
            $three_d_weeklyTotal = Lotto::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('total_amount');
            $three_d_monthlyTotal = Lotto::whereMonth('created_at', '=', now()->month)
                ->whereYear('created_at', '=', now()->year)
                ->sum('total_amount');
            $three_d_yearlyTotal = Lotto::whereYear('created_at', '=', now()->year)->sum('total_amount');

            $lottery_matches = LotteryMatch::where('is_active', true)->first();

            // Return data to admin dashboard
            return view('admin.dashboard', [
                'dailyTotal' => $dailyTotal,
                'weeklyTotal' => $weeklyTotal,
                'monthlyTotal' => $monthlyTotal,
                'yearlyTotal' => $yearlyTotal,
                'three_d_dailyTotal' => $three_d_dailyTotal,
                'three_d_weeklyTotal' => $three_d_weeklyTotal,
                'three_d_monthlyTotal' => $three_d_monthlyTotal,
                'three_d_yearlyTotal' => $three_d_yearlyTotal,
                'lottery_matches' => $lottery_matches,
            ]);
        } else {
            $evening_data = $this->eveningLotteryService->TwoDEveningHistory();
            $morning_data = $this->morningLotteryService->MorningHistory();
            $lottery_winner = $this->winnerSevice->LotteryWinnersPrize();
            // Data for morning session
            $morning_results = $morning_data['results'] ?? collect();
            $morning_total = $morning_data['totalSubAmount'] ?? 0;

            // Data for evening session
            $evening_results = $evening_data['results'] ?? collect();
            $evening_total = $evening_data['totalSubAmount'] ?? 0;
            Log::info('Evening Results:', ['results' => $evening_results]);
            Log::info('Evening Total:', ['total' => $evening_total]);

            // Pass the data to the view
            return view('frontend.user-profile', [
                'morning_results' => $morning_results,
                'morning_total' => $morning_total,
                'evening_results' => $evening_results,
                'evening_total' => $evening_total,
                'lottery_winner' => $lottery_winner['results'],  // List of winners
                'totalPrizeAmount' => $lottery_winner['totalPrizeAmount'],  // Total prize amount
            ]);
        }
    }

    public function UserPlayEveningRecord()
    {
        $userId = auth()->id(); // Get logged in user's ID
        //$playedMorningTwoDigits = User::getUserMorningTwoDigits($userId);
        $playedEveningTwoDigits = User::getUserEveningTwoDigits($userId);

        return view('frontend.user_play_evening', [
            //'morningDigits' => $playedMorningTwoDigits,
            'eveningDigits' => $playedEveningTwoDigits,
        ]);
    }

    public function profile()
    {
        return view('frontend.user-profile');
    }
}
