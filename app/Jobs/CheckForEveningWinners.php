<?php

namespace App\Jobs;

use App\Models\Lottery;
use App\Models\LotteryTwoDigitPivot;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckForEveningWinners implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $twodWiner;

    public function __construct($twodWiner)
    {
        $this->twodWiner = $twodWiner;
    }

    public function handle()
    {
        Log::info('CheckForMorningWinners job started');

        $today = Carbon::today();
        $playDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        if (! in_array(strtolower($today->isoFormat('dddd')), $playDays)) {
            Log::info('Today is not a play day: '.$today->isoFormat('dddd'));

            return; // Not a play day
        }

        if ($this->twodWiner->session !== 'evening') {
            Log::info('Session is not evening, exiting.');

            return; // Not a morning session
        }

        // Get the correct bet digit from result number
        $result_number = $this->twodWiner->result_number;

        // Retrieve winning entries where bet_digit matches result_number
        $winningEntries = LotteryTwoDigitPivot::where('bet_digit', $result_number)
            ->where('match_status', 'open')
            ->whereDate('created_at', $today)
            ->get();

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                try {
                    $lottery = Lottery::findOrFail($entry->lottery_id);
                    $user = $lottery->user;

                    $prize = $entry->sub_amount * 85;
                    $user->balance += $prize; // Correct, user is an Eloquent model
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

        Log::info('CheckForMorningWinners job completed.');
    }
}
