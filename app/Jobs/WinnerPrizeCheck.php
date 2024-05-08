<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\ThreeDigit\Lotto;
use App\Models\ThreeDigit\ResultDate;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WinnerPrizeCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prize;

    public function __construct($prize)
    {
        $this->prize = $prize;
    }

    public function handle(): void
    {
        Log::info('WinnerPrizeCheck job started');

        // Ensure prize object is valid and has the required attributes
        if (! isset($this->prize->prize_one) || ! isset($this->prize->prize_two)) {
           // Log::warning('Invalid prize object provided. Exiting job.');
            return;
        }

        $open_dates = ResultDate::where('status', 'open')->get();
        if ($open_dates->isEmpty()) {
            Log::warning('No open result dates found.');
            return;
        }

        // Collect open date IDs
        $date_ids = $open_dates->pluck('id')->toArray();

        // Process winning entries for prize_one and prize_two
        $this->processWinningEntries($this->prize->prize_one, $date_ids);
        $this->processWinningEntries($this->prize->prize_two, $date_ids);

        Log::info('WinnerPrizeCheck job completed.');
    }

    protected function processWinningEntries($prize_digit, array $date_ids)
    {
        if (empty($prize_digit)) {
            //Log::warning('Empty prize_digit provided. Skipping processing.');
            return;
        }

        $today = Carbon::today(); // Current date
        $winningEntries = LotteryThreeDigitPivot::whereIn('result_date_id', $date_ids)
            ->where('bet_digit', $prize_digit)
            ->whereDate('created_at', $today)
            ->get();

        if ($winningEntries->isEmpty()) {
           // Log::info("No winning entries found for bet_digit: {$prize_digit} on date: {$today->toDateString()}");
            return;
        }

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                try {
                    $lottery = Lotto::findOrFail($entry->lotto_id);
                    $user = $lottery->user;

                    $prize = $entry->sub_amount * 10; // Calculate the prize amount
                    $user->balance += $prize; // Update user balance
                    $user->save(); // Save updated user balance

                    $entry->prize_sent = 3; // Mark as prize sent
                    $entry->save();

                    //Log::info("Prize awarded and prize_sent set to 3 for entry ID {$entry->id}.");
                } catch (\Exception $e) {
                    Log::error("Error during transaction for entry ID {$entry->id}: {$e->getMessage()}");
                    throw $e; // Ensure rollback if needed
                }
            });
        }
    }
}