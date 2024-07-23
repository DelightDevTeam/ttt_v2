<?php

namespace App\Http\Controllers\Admin\ThreeD;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LottoWinnerHistoryController extends Controller
{
    public function getGroupedByRunningMatch()
    {
        $reports = DB::table('lotto_three_digit_pivot')
            ->select('running_match', DB::raw('COUNT(*) as total_records'), DB::raw('SUM(sub_amount) as total_amount'))
            ->where('prize_sent', true) // Filter where prize_sent is true
            ->groupBy('running_match')
            ->orderByDesc('running_match') // Optional: order by running_match descending
            ->get();

        return view('admin.three_d.report.first_winner.index', compact('reports'));

        //return response()->json($results);
    }

    public function getFirstPrizeDetailsByRunningMatch($running_match)
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
            ->where('lotto_three_digit_pivot.prize_sent', true)
            ->orderByDesc('lotto_three_digit_pivot.created_at')
            ->get();

        return view('admin.three_d.report.first_winner.show', compact('reports'));

        //return response()->json($details);
    }

    public function getSecondPrizeGroupedByRunningMatch()
    {
        $reports = DB::table('lotto_three_digit_pivot')
            ->select('running_match', DB::raw('COUNT(*) as total_records'), DB::raw('SUM(sub_amount) as total_amount'))
            ->where('prize_sent', 2) // Filter where prize_sent is true
            ->groupBy('running_match')
            ->orderByDesc('running_match') // Optional: order by running_match descending
            ->get();

        return view('admin.three_d.report.second_winner.index', compact('reports'));

        //return response()->json($results);
    }

    public function getSecondPrizeDetailsByRunningMatch($running_match)
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
            ->where('lotto_three_digit_pivot.prize_sent', true)
            ->orderByDesc('lotto_three_digit_pivot.created_at')
            ->get();

        return view('admin.three_d.report.second_winner.show', compact('reports'));

        //return response()->json($details);
    }

    public function getThirdPrizeGroupedByRunningMatch()
    {
        $reports = DB::table('lotto_three_digit_pivot')
            ->select('running_match', DB::raw('COUNT(*) as total_records'), DB::raw('SUM(sub_amount) as total_amount'))
            ->where('prize_sent', 3) // Filter where prize_sent is true
            ->groupBy('running_match')
            ->orderByDesc('running_match') // Optional: order by running_match descending
            ->get();

        return view('admin.three_d.report.third_winner.index', compact('reports'));

        //return response()->json($results);
    }

    public function getThirdPrizeDetailsByRunningMatch($running_match)
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
            ->where('lotto_three_digit_pivot.prize_sent', true)
            ->orderByDesc('lotto_three_digit_pivot.created_at')
            ->get();

        return view('admin.three_d.report.third_winner.show', compact('reports'));

        //return response()->json($details);
    }
}
