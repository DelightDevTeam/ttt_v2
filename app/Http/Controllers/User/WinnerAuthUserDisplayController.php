<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AuthPermutationOneWeekWinnerService;
use App\Services\OneWeekPrizeSentService;
use App\Services\WinPrizeSentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WinnerAuthUserDisplayController extends Controller
{
    protected $firstPrize;

    protected $winnerService;

    protected $prizeSentSevices;

    public function __construct(AuthPermutationOneWeekWinnerService $winnerService, OneWeekPrizeSentService $firstPrize, WinPrizeSentService $prizeSentSevices)
    {
        $this->winnerService = $winnerService;
        $this->firstPrize = $firstPrize;
        $this->prizeSentSevices = $prizeSentSevices;
    }

    public function displayWinners()
    {
        $prizes = $this->firstPrize->OneWeekWinner();
        $data = $this->winnerService->OneWeekPermutationWinner();
        $prise_sents = $this->prizeSentSevices->OneWeekWinnerPrize();

        return view('three_d.three_d_winner_display',
            [
                'winners' => $data,
                'first_prizes' => $prizes,
                'prise_sents' => $prise_sents,
            ]);
    }

    // public function getWinnersHistoryForAuthUserOnly()
    // {
    //     $oneMonthAgo = Carbon::now()->subMonth();
    //     $userId = auth()->id(); // Get the authenticated user's ID

    //     $winners = DB::table('lotto_three_digit_pivot')
    //         ->join('three_digits', 'lotto_three_digit_pivot.three_digit_id', '=', 'three_digits.id')
    //         ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
    //         ->join('users', 'lottos.user_id', '=', 'users.id')
    //         ->join('three_winners', 'three_digits.three_digit', '=', 'three_winners.prize_no')
    //         ->where('lottos.user_id', $userId) // Add this line to filter by the authenticated user's ID
    //         ->whereDate('three_winners.created_at', '>=', $oneMonthAgo)
    //         ->groupBy(
    //             'lottos.user_id',
    //             'users.name',
    //             'users.profile',
    //             'users.phone',
    //             'lotto_three_digit_pivot.sub_amount',
    //             'lotto_three_digit_pivot.prize_sent',
    //             'lottos.total_amount',
    //             'three_winners.prize_no',
    //             'three_winners.created_at'
    //         )
    //         ->select(
    //             'lottos.user_id',
    //             'users.name',
    //             'users.profile',
    //             'users.phone',
    //             'lotto_three_digit_pivot.sub_amount',
    //             'lotto_three_digit_pivot.prize_sent',
    //             'lottos.total_amount',
    //             'three_winners.prize_no',
    //             'three_winners.created_at',
    //             DB::raw('lotto_three_digit_pivot.sub_amount * 700 as prize_amount')
    //         )
    //         ->orderBy('prize_amount', 'desc')
    //         ->get();

    //     // Update the prize_sent date for each winner
    //     foreach ($winners as $winner) {
    //         $this->updatePrizeSentDate($winner->user_id); // Make sure user_id is the ID of the winner
    //     }

    //     $MonthAgo = Carbon::now()->subMonth();
    //     $permutation_winners = DB::table('lotto_three_digit_pivot')
    //         ->join('three_digits', 'lotto_three_digit_pivot.three_digit_id', '=', 'three_digits.id')
    //         ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
    //         ->join('users', 'lottos.user_id', '=', 'users.id')
    //         ->join('permutations', 'three_digits.three_digit', '=', 'permutations.digit')
    //         ->where('lottos.user_id', $userId) // Add this line to filter by the authenticated user's ID
    //         ->whereDate('permutations.created_at', '>=', $MonthAgo)
    //         ->groupBy(
    //             'lottos.user_id',
    //             'users.name',
    //             'users.profile',
    //             'users.phone',
    //             'lotto_three_digit_pivot.sub_amount',
    //             'lotto_three_digit_pivot.prize_sent',
    //             'lottos.total_amount',
    //             'permutations.digit',
    //             'permutations.created_at',
    //         )
    //         ->select(
    //             'lottos.user_id',
    //             'users.name',
    //             'users.profile',
    //             'users.phone',
    //             'lotto_three_digit_pivot.sub_amount',
    //             'lotto_three_digit_pivot.prize_sent',
    //             'lottos.total_amount',
    //             'permutations.digit',
    //             'permutations.created_at',
    //             DB::raw('lotto_three_digit_pivot.sub_amount * 10 as prize_amount')
    //         )
    //         ->orderBy('prize_amount', 'desc') // Add this line to sort by prize_amount in descending order
    //         ->get();
    //     // Update the prize_sent date for each winner
    //     foreach ($permutation_winners as $permutation) {
    //         $this->updatePermutationPrizeSentDate($permutation->user_id); // Make sure user_id is the ID of the winner
    //     }

    //     return view('three_d.three_d_winner_display', compact('winners', 'permutation_winners'));
    // }

    // public function updatePrizeSentDate($winnerId)
    // {
    //     // Find the lottery_two_digit_pivot record
    //     $lotteryTwoDigitPivot = DB::table('lotto_three_digit_pivot')->where('lotto_id', $winnerId)->first();

    //     // Check if the record exists
    //     if (! $lotteryTwoDigitPivot) {
    //         return redirect()->back()->with('error', 'Record not found!');
    //     }

    //     // Update the prize_sent field to true
    //     DB::table('lotto_three_digit_pivot')
    //         ->where('lotto_id', $winnerId)
    //         ->update(['prize_sent' => true]);

    //     return redirect()->back()->with('success', 'Prize sent date updated successfully!');
    // }

    // public function updatePermutationPrizeSentDate($winnerId)
    // {
    //     // Find the lottery_two_digit_pivot record
    //     $lotteryTwoDigitPivot = DB::table('lotto_three_digit_pivot')->where('lotto_id', $winnerId)->first();

    //     // Check if the record exists
    //     if (! $lotteryTwoDigitPivot) {
    //         return redirect()->back()->with('error', 'Record not found!');
    //     }

    //     // Update the prize_sent field to true
    //     DB::table('lotto_three_digit_pivot')
    //         ->where('lotto_id', $winnerId)
    //         ->update(['prize_sent' => 2]);

    //     return redirect()->back()->with('success', 'Prize sent date updated successfully!');
    // }
}
