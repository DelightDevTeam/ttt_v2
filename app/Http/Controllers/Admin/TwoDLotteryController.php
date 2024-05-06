<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TwodWiner;
use App\Models\TwoD\Lottery;
use App\Models\TwoD\LotteryTwoDigitPivot;
use App\Models\TwoD\TwodGameResult;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TwoDLotteryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all lotteries with related user data
        $lotteries = LotteryTwoDigitPivot::with('user')->orderBy('id', 'desc')->get();

        // Calculate the total sub_amount grouped by session (morning and evening)
        $totalSubAmountBySession = LotteryTwoDigitPivot::selectRaw('session, SUM(sub_amount) as total_sub_amount')
            ->groupBy('session') // Group results by session
            ->get();

        // Calculate the total win amounts, grouped by session
        $totalWinAmountBySession = LotteryTwoDigitPivot::where('prize_sent', true)
            ->selectRaw('session, SUM(sub_amount * 85) as total_win_amount') // Calculate total win amount
            ->groupBy('session') // Group results by session
            ->get();

        // Create an associative array for win amounts
        $winAmounts = [];
        foreach ($totalWinAmountBySession as $item) {
            $winAmounts[$item->session] = $item->total_win_amount;
        }

        // Prepare a simple associative array for easier use in the view
        $sessionTotals = $totalSubAmountBySession->pluck('total_sub_amount', 'session');

        // Pass the required data to the view
        return view('admin.two_d.two_d_history', compact('lotteries', 'sessionTotals', 'winAmounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lottery = Lottery::with('twoDigits')->findOrFail($id);
        $prize_no = TwodWiner::whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->first();

        return view('admin.two_d.two_history_show', compact('lottery', 'prize_no'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

/*
    $startOfDay = Carbon::now()->startOfDay();
    $endOfDay = Carbon::now()->endOfDay();

    // Determine AM or PM
    $currentMeridiem = Carbon::now()->format('A');  // This will return either "AM" or "PM"

    // Find the prize_no based on today's date and AM/PM
    $prize_no = TwodWiner::whereBetween('created_at', [$startOfDay, $endOfDay])
                         ->where('prize_no', 'LIKE', '%' . $currentMeridiem . '%')
                         ->first();
    */
