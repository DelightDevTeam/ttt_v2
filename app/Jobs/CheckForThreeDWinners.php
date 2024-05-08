<?php

namespace App\Jobs;

use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Lotto;
use App\Models\ThreeDigit\ResultDate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckForThreeDWinners implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $three_d_winner;

    public function __construct($three_d_winner)
    {
        $this->three_d_winner = $three_d_winner;
    }

    public function handle()
    {
        //Log::info('CheckFor3DWinners job started');

        $today = Carbon::today();

        // Get the correct bet digit from result number
        $result_number = $this->three_d_winner->result_number;

        $open_date = ResultDate::where('status', 'open')
            ->get();
        // Correctly accumulate IDs into an array
        $dates = []; // Initialize an array
        foreach ($open_date as $date) {
            $dates[] = $date->id; // Add each ID to the array
        }

        //Log::info('Open result date IDs:', ['dates' => $dates]);

        // Check if the $dates array is empty
        if (empty($dates) || ! is_array($dates)) {
            //Log::warning('No open result dates found or $dates is not an array');
            return; // Exit the function if no valid open dates
        }
        // Retrieve winning entries where bet_digit matches result_number
        // $winningEntries = LotteryThreeDigitPivot::where('bet_digit', $result_number)
        //     ->where('prize_sent', false)
        //     ->where('lotto_three_digit_pivot.result_date_id', $dates)
        //     ->whereDate('created_at', $today)
        //     ->get();
        $winningEntries = LotteryThreeDigitPivot::whereIn('result_date_id', $dates)
            ->where('prize_sent', false)
            ->where('bet_digit', $result_number) // Make sure this is correct
            ->whereDate('created_at', $today)
            ->get(); // Fetch the results

        // $winningEntries = DB::table('lotto_three_digit_pivot')
        //     ->join('lottos', 'lotto_three_digit_pivot.lotto_id', '=', 'lottos.id')
        //     ->join('result_dates', 'lotto_three_digit_pivot.result_date_id', '=', 'result_dates.id')
        //     ->where('result_dates.status', 'open')
        //     ->where('result_dates.id', $result_number)
        //     ->where('lotto_three_digit_pivot.bet_digit', $result_number)
        //     ->where('lotto_three_digit_pivot.prize_sent', 0)
        //     ->whereDate('lotto_three_digit_pivot.created_at', $today)
        //     ->select('lotto_three_digit_pivot.*') // Select all columns from pivot table
        //     ->get();

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                try {
                    $lottery = Lotto::findOrFail($entry->lotto_id);
                    if (! $lottery) {
                        //Log::error("Lotto entry not found for ID: {$entry->lotto_id}");

                        return; // Skip this entry if not found
                    }
                    $user = $lottery->user;

                    $prize = $entry->sub_amount * 700;
                    $user->balance += $prize; // Correct, user is an Eloquent model
                    $user->prize_balance += $prize;
                    $user->save();

                    // Now the entry is also an Eloquent model, so this works
                    $entry->prize_sent = true;
                    $entry->save();
                } catch (\Exception $e) {
                    Log::error("Error during transaction for entry ID {$entry->id}: ".$e->getMessage());
                    throw $e; // Ensure rollback if needed
                }
            });
        }

        //Log::info('CheckFor3DWinners job completed.');
    }
}