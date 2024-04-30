<?php

namespace App\Jobs;

use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Lotto;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckForThreeDWinnersWithPermutations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $threedWinner;

    public function __construct($threedWinner)
    {
        $this->threedWinner = $threedWinner;
    }

    public function handle()
    {
        Log::info('CheckForThreeDWinnersWithPermutations job started');

        $today = Carbon::today();
        $result_number = $this->threedWinner->result_number;

        if (! $result_number) {
            Log::info('No result number provided. Exiting job.');

            return;
        }

        // Generate permutations for the three-digit result number, excluding the original
        $permutations = $this->generatePermutationsExcludeOriginal($result_number);

        foreach ($permutations as $permutation) {
            $this->processWinningEntries($permutation);
        }

        Log::info('CheckForThreeDWinnersWithPermutations job completed.');
    }

    protected function processWinningEntries($permutation)
    {
        $today = Carbon::today();

        $winningEntries = LotteryThreeDigitPivot::where('bet_digit', $permutation)
            ->where('match_status', 'open')
            ->whereDate('created_at', $today)
            ->get();

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                try {
                    $lottery = Lotto::findOrFail($entry->lotto_id);
                    $user = $lottery->user;

                    $prize = $entry->sub_amount * 10; // Calculate the prize amount
                    $user->balance += $prize; // Increase the user's balance
                    $user->save();

                    $entry->prize_sent = 2; // Mark as prize sent
                    $entry->save();

                    Log::info("Prize awarded and prize_sent set to true for entry ID {$entry->id}.");
                } catch (\Exception $e) {
                    Log::error("Error during transaction for entry ID {$entry->id}: ".$e->getMessage());
                    throw $e; // Trigger rollback if needed
                }
            });
        }
    }

    protected function generatePermutationsExcludeOriginal($original)
    {
        $permutations = $this->permutation($original);

        if (($key = array_search($original, $permutations)) !== false) {
            unset($permutations[$key]); // Remove the original from permutations
        }

        return array_values($permutations); // Return the remaining permutations
    }

    protected function permutation($str)
    {
        if (strlen($str) <= 1) {
            return [$str];
        }

        $result = [];
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            $remainingChars = substr($str, 0, $i).substr($str, $i + 1);

            foreach ($this->permutation($remainingChars) as $subPerm) {
                $result[] = $char.$subPerm;
            }
        }

        return array_unique($result); // Ensure unique permutations
    }
}
