<?php

namespace App\Http\Controllers\Admin\ThreeD;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\ThreeDigit;
use App\Models\ThreeDigit\ThreeDLimit;

class GetLottoDataByRunningMatchController extends Controller
{
    public function getDetailedGroupedByRunningMatch()
    {
        $results = DB::table('lotto_three_digit_pivot')
            ->join('users', 'lotto_three_digit_pivot.user_id', '=', 'users.id')
            ->select(
                'lotto_three_digit_pivot.running_match',
                'users.user_name',
                'users.phone',
                'lotto_three_digit_pivot.agent_id',
                'lotto_three_digit_pivot.bet_digit',
                'lotto_three_digit_pivot.sub_amount',
                'lotto_three_digit_pivot.res_date',
                'lotto_three_digit_pivot.res_time',
                'lotto_three_digit_pivot.play_date',
                'lotto_three_digit_pivot.play_time',
                'lotto_three_digit_pivot.match_start_date',
                'lotto_three_digit_pivot.match_status',
                'lotto_three_digit_pivot.win_lose',
                'lotto_three_digit_pivot.prize_sent',
                DB::raw('COUNT(*) as total_records'),
                DB::raw('SUM(lotto_three_digit_pivot.sub_amount) as total_amount')
            )
            ->groupBy(
                'lotto_three_digit_pivot.running_match',
                'users.user_name',
                'users.phone',
                'lotto_three_digit_pivot.agent_id',
                'lotto_three_digit_pivot.bet_digit',
                'lotto_three_digit_pivot.sub_amount',
                'lotto_three_digit_pivot.res_date',
                'lotto_three_digit_pivot.res_time',
                'lotto_three_digit_pivot.play_date',
                'lotto_three_digit_pivot.play_time',
                'lotto_three_digit_pivot.match_start_date',
                'lotto_three_digit_pivot.match_status',
                'lotto_three_digit_pivot.win_lose',
                'lotto_three_digit_pivot.prize_sent'
            )
            ->get();

        return response()->json($results);
    }

    public function index(Request $request)
    {
        $reports = DB::table('lotto_three_digit_pivot')
            ->join('users', 'lotto_three_digit_pivot.user_id', '=', 'users.id')
            ->select(
                'lotto_three_digit_pivot.running_match',
                'users.user_name',
                'users.phone',
                'lotto_three_digit_pivot.agent_id',
                'lotto_three_digit_pivot.bet_digit',
                'lotto_three_digit_pivot.sub_amount',
                'lotto_three_digit_pivot.res_date',
                'lotto_three_digit_pivot.res_time',
                'lotto_three_digit_pivot.play_date',
                'lotto_three_digit_pivot.play_time',
                'lotto_three_digit_pivot.match_start_date',
                'lotto_three_digit_pivot.match_status',
                'lotto_three_digit_pivot.win_lose',
                'lotto_three_digit_pivot.prize_sent',
                DB::raw('COUNT(*) as total_records'),
                DB::raw('SUM(lotto_three_digit_pivot.sub_amount) as total_amount')
            )
            ->groupBy(
                'lotto_three_digit_pivot.running_match',
                'users.user_name',
                'users.phone',
                'lotto_three_digit_pivot.agent_id',
                'lotto_three_digit_pivot.bet_digit',
                'lotto_three_digit_pivot.sub_amount',
                'lotto_three_digit_pivot.res_date',
                'lotto_three_digit_pivot.res_time',
                'lotto_three_digit_pivot.play_date',
                'lotto_three_digit_pivot.play_time',
                'lotto_three_digit_pivot.match_start_date',
                'lotto_three_digit_pivot.match_status',
                'lotto_three_digit_pivot.win_lose',
                'lotto_three_digit_pivot.prize_sent'
            )
            ->orderByDesc('lotto_three_digit_pivot.running_match')
            ->get();

        return view('admin.three_d.report.index', compact('reports'));
    }

    public function getGroupedByRunningMatch()
    {
        $reports = DB::table('lotto_three_digit_pivot')
            ->select('running_match', DB::raw('COUNT(*) as total_records'), DB::raw('SUM(sub_amount) as total_amount'))
            ->groupBy('running_match')
            ->get();

        return view('admin.three_d.report.index', compact('reports'));

        //return response()->json($results);
    }

    public function getDetailsByRunningMatch($running_match)
    {
        $reports = DB::table('lotto_three_digit_pivot')
            ->join('users', 'lotto_three_digit_pivot.user_id', '=', 'users.id')
            ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
            ->select(
                'users.user_name',
                'users.phone',
                'lotto_three_digit_pivot.agent_id',
                'lotto_three_digit_pivot.bet_digit',
                'lotto_three_digit_pivot.sub_amount',
                'lotto_three_digit_pivot.res_date',
                'lotto_three_digit_pivot.res_time',
                'lotto_three_digit_pivot.play_date',
                'lotto_three_digit_pivot.play_time',
                'lotto_three_digit_pivot.match_start_date',
                'lotto_three_digit_pivot.match_status',
                'lotto_three_digit_pivot.win_lose',
                'lotto_three_digit_pivot.prize_sent',
                'lottos.slip_no'
            )
            ->where('lotto_three_digit_pivot.running_match', $running_match)
            ->orderByDesc('lotto_three_digit_pivot.created_at')
            ->get();

        return view('admin.three_d.report.show', compact('reports'));

        //return response()->json($details);
    }

    public function LottoLegar($running_match)
    {
        $defaultBreak = ThreeDLimit::lasted()->first();

        // Retrieve all three-digit numbers from the ThreeDigit model
        $threeDigitNumbers = ThreeDigit::all()->pluck('three_digit');

        $reports = DB::table('lotto_three_digit_pivot')
            ->join('users', 'lotto_three_digit_pivot.user_id', '=', 'users.id')
            ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
            ->select(
                'users.user_name',
                'users.phone',
                'lotto_three_digit_pivot.agent_id',
                'lotto_three_digit_pivot.bet_digit',
                'lotto_three_digit_pivot.sub_amount',
                'lotto_three_digit_pivot.res_date',
                'lotto_three_digit_pivot.res_time',
                'lotto_three_digit_pivot.play_date',
                'lotto_three_digit_pivot.play_time',
                'lotto_three_digit_pivot.match_start_date',
                'lotto_three_digit_pivot.match_status',
                'lotto_three_digit_pivot.win_lose',
                'lotto_three_digit_pivot.prize_sent',
                'lottos.slip_no'
            )
            ->where('lotto_three_digit_pivot.running_match', $running_match)
            ->orderByDesc('lotto_three_digit_pivot.created_at')
            ->get();

        // Calculate the sub amounts for each digit
        $subAmounts = DB::table('lotto_three_digit_pivot')
            ->select(DB::raw('LEFT(bet_digit, 3) as three_digit'), DB::raw('SUM(sub_amount) as total_sub_amount'))
            ->where('running_match', $running_match)
            ->groupBy(DB::raw('LEFT(bet_digit, 3)'))
            ->pluck('total_sub_amount', 'three_digit');

        // Combine the threeDigitNumbers numbers with their sub-amounts
        $twoDigitData = $threeDigitNumbers->map(function ($digit) use ($subAmounts) {
            return [
                'digit' => $digit,
                'total_sub_amount' => $subAmounts->get(substr($digit, 0, 3), 0),
            ];
        });

        return view('admin.three_d.report.lager_show', compact('reports', 'twoDigitData', 'defaultBreak'));
    }
}
