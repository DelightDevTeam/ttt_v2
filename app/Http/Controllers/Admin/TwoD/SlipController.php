<?php

namespace App\Http\Controllers\Admin\TwoD;

use App\Http\Controllers\Controller;
use App\Models\TwoD\Lottery;
use App\Models\TwoD\LotteryTwoDigitPivot;
use App\Models\TwoD\TwodGameResult;
use App\Models\TwoD\TwodSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SlipController extends Controller
{
    public function index()
    {
        // Get the match start date and result date from TwodSetting
        $draw_date = TwodGameResult::where('status', 'open')->first();
        if (! $draw_date) {
            return view('admin.two_d.slip.morning_slip', [
                'records' => collect([]),
                'total_amount' => 0,
                'error' => 'No open draw date found.',
            ]);
        }

        $start_date = $draw_date->result_date;

        // Log the start date and conditions for debugging
        Log::info('Start date:', ['start_date' => $start_date]);

        // Enable query logging
        DB::enableQueryLog();

        // Retrieve and group records by user_id within the specified date range
        $records = LotteryTwoDigitPivot::with('user')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivot.res_date', $start_date)
            ->where('lottery_two_digit_pivot.session', 'morning')
            ->select('lottery_two_digit_pivot.user_id', 'lotteries.slip_no', DB::raw('SUM(lottery_two_digit_pivot.sub_amount) as total_sub_amount'))
            ->groupBy('lottery_two_digit_pivot.user_id', 'lotteries.slip_no')
            ->get();

        // Log the retrieved records for debugging
        Log::info('Retrieved records:', ['records' => $records]);

        // Log the actual SQL query executed
        $queries = DB::getQueryLog();
        Log::info('Executed query:', ['queries' => $queries]);

        // Calculate the total amount from the lotteries table within the date range
        //$total_amount = Lottery::whereDate('created_at', $start_date)->sum('total_amount');
        $total_amount = Lottery::whereDate('created_at', $start_date)
            ->where('session', 'morning')
            ->sum('total_amount');
        // Log the total amount for debugging
        Log::info('Total amount:', ['total_amount' => $total_amount]);

        // Return the records to your view
        return view('admin.two_d.slip.morning_slip', compact('records', 'total_amount'));
    }

    public function show($user_id, $slip_no)
    {
        // Retrieve records for a specific user_id and slip_no
        $records = LotteryTwoDigitPivot::with('user')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivot.user_id', $user_id)
            ->where('lotteries.slip_no', $slip_no)
            ->select('lottery_two_digit_pivot.*', 'lotteries.slip_no')
            ->get();

        // Calculate the total sub_amount for the specific user_id and slip_no
        $total_sub_amount = $records->sum('sub_amount');

        return view('admin.two_d.slip.morning_slip_show', compact('records', 'total_sub_amount', 'slip_no', 'user_id'));
    }

    public function Eveningindex()
    {
        // Get the match start date and result date from TwodSetting
        $draw_date = TwodGameResult::where('status', 'open')->first();
        if (! $draw_date) {
            return view('admin.two_d.slip.evening_slip', [
                'records' => collect([]),
                'total_amount' => 0,
                'error' => 'No open draw date found.',
            ]);
        }

        $start_date = $draw_date->result_date;

        // Log the start date and conditions for debugging
        Log::info('Start date:', ['start_date' => $start_date]);

        // Enable query logging
        DB::enableQueryLog();

        // Retrieve and group records by user_id within the specified date range
        $records = LotteryTwoDigitPivot::with('user')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivot.res_date', $start_date)
            ->where('lottery_two_digit_pivot.session', 'evening')
            ->select('lottery_two_digit_pivot.user_id', 'lotteries.slip_no', DB::raw('SUM(lottery_two_digit_pivot.sub_amount) as total_sub_amount'))
            ->groupBy('lottery_two_digit_pivot.user_id', 'lotteries.slip_no')
            ->get();

        // Log the retrieved records for debugging
        Log::info('Retrieved records:', ['records' => $records]);

        // Log the actual SQL query executed
        $queries = DB::getQueryLog();
        Log::info('Executed query:', ['queries' => $queries]);

        // Calculate the total amount from the lotteries table within the date range
        //$total_amount = Lottery::whereDate('created_at', $start_date)->sum('total_amount');
        // Calculate the total amount for the morning session
        $total_amount = Lottery::whereDate('created_at', $start_date)
            ->where('session', 'evening')
            ->sum('total_amount');
        // Log the total amount for debugging
        Log::info('Total amount:', ['total_amount' => $total_amount]);

        // Return the records to your view
        return view('admin.two_d.slip.evening_slip', compact('records', 'total_amount'));
    }

    public function Eveningshow($user_id, $slip_no)
    {
        $records = LotteryTwoDigitPivot::with('user')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivot.user_id', $user_id)
            ->where('lotteries.slip_no', $slip_no)
            ->select('lottery_two_digit_pivot.*', 'lotteries.slip_no')
            ->get();

        // Calculate the total sub_amount for the specific user_id and slip_no
        $total_sub_amount = $records->sum('sub_amount');

        return view('admin.two_d.slip.evening_slip_show', compact('records', 'total_sub_amount', 'slip_no', 'user_id'));
    }

    public function AllSlipForMorningindex()
    {
        // Enable query logging
        DB::enableQueryLog();

        // Retrieve and group records by user_id for the morning session
        $records = LotteryTwoDigitPivot::with('user')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivot.session', 'morning')
            ->select('lottery_two_digit_pivot.user_id', 'lottery_two_digit_pivot.res_date', 'lotteries.slip_no', DB::raw('SUM(lottery_two_digit_pivot.sub_amount) as total_sub_amount'))
            ->groupBy('lottery_two_digit_pivot.user_id', 'lottery_two_digit_pivot.res_date', 'lotteries.slip_no')
            ->get();

        // Log the retrieved records for debugging
        Log::info('Retrieved records:', ['records' => $records]);

        // Log the actual SQL query executed
        $queries = DB::getQueryLog();
        Log::info('Executed query:', ['queries' => $queries]);

        // Calculate the total amount for the morning session from the lotteries table
        $total_amount = Lottery::where('session', 'morning')
            ->sum('total_amount');

        // Log the total amount for debugging
        Log::info('Total amount:', ['total_amount' => $total_amount]);

        // Return the records to your view
        return view('admin.two_d.slip.morning_all_slip', compact('records', 'total_amount'));
    }

    public function MorningAllSlipshow($user_id, $slip_no)
    {
        $records = LotteryTwoDigitPivot::with('user')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivot.user_id', $user_id)
            ->where('lotteries.slip_no', $slip_no)
            ->select('lottery_two_digit_pivot.*', 'lotteries.slip_no')
            ->get();

        // Calculate the total sub_amount for the specific user_id and slip_no
        $total_sub_amount = $records->sum('sub_amount');

        return view('admin.two_d.slip.morning_all_slip_show', compact('records', 'total_sub_amount', 'slip_no', 'user_id'));
    }

    public function AllSlipForEveningindex()
    {
        // Enable query logging
        DB::enableQueryLog();

        // Retrieve and group records by user_id for the morning session
        $records = LotteryTwoDigitPivot::with('user')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivot.session', 'evening')
            ->select('lottery_two_digit_pivot.user_id', 'lottery_two_digit_pivot.res_date', 'lotteries.slip_no', DB::raw('SUM(lottery_two_digit_pivot.sub_amount) as total_sub_amount'))
            ->groupBy('lottery_two_digit_pivot.user_id', 'lottery_two_digit_pivot.res_date', 'lotteries.slip_no')
            ->get();

        // Log the retrieved records for debugging
        Log::info('Retrieved records:', ['records' => $records]);

        // Log the actual SQL query executed
        $queries = DB::getQueryLog();
        Log::info('Executed query:', ['queries' => $queries]);

        // Calculate the total amount for the morning session from the lotteries table
        $total_amount = Lottery::where('session', 'evening')
            ->sum('total_amount');

        // Log the total amount for debugging
        Log::info('Total amount:', ['total_amount' => $total_amount]);

        // Return the records to your view
        return view('admin.two_d.slip.evening_all_slip', compact('records', 'total_amount'));
    }

    public function EveningAllSlipshow($user_id, $slip_no)
    {
        $records = LotteryTwoDigitPivot::with('user')
            ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivot.user_id', $user_id)
            ->where('lotteries.slip_no', $slip_no)
            ->select('lottery_two_digit_pivot.*', 'lotteries.slip_no')
            ->get();

        // Calculate the total sub_amount for the specific user_id and slip_no
        $total_sub_amount = $records->sum('sub_amount');

        return view('admin.two_d.slip.evening_all_slip_show', compact('records', 'total_sub_amount', 'slip_no', 'user_id'));
    }
}
