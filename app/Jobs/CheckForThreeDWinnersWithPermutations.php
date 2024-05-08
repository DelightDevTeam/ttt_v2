<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Models\ThreeDigit\Lotto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ThreeDigit\ResultDate;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;

class CheckForThreeDWinnersWithPermutations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $threedWinner;

    public function __construct($threedWinner)
    {
        $this->threedWinner = $threedWinner;
    }

    // public function handle()
    // {
    //     Log::info('CheckForThreeDWinnersWithPermutations job started');

    //     $today = Carbon::today();
    //     $result_number = $this->threedWinner->result_number;

        

    //     if (! $result_number) {
    //         Log::info('No result number provided. Exiting job.');

    //         return;
    //     }

    //     // Generate permutations for the three-digit result number, excluding the original
    //     $permutations = $this->generatePermutationsExcludeOriginal($result_number);

    //     foreach ($permutations as $permutation) {
    //         $this->processWinningEntries($permutation);
    //     }

    //     Log::info('CheckForThreeDWinnersWithPermutations job completed.');
    // }

     public function handle()
    {
        //Log::info('CheckForThreeDWinnersWithPermutations job started');

        $today = Carbon::today(); // Get today's date
        $result_number = $this->threedWinner->result_number ?? null;

        if (is_null($result_number)) {
           // Log::info('No result number provided. Exiting job.');
            return;
        }

        // Generate permutations for the three-digit result number, excluding the original
        $permutations = $this->generatePermutationsExcludeOriginal($result_number);

        // Fetch open dates for processing
        $open_dates = ResultDate::where('status', 'open')->get();
        if ($open_dates->isEmpty()) {
            Log::warning('No open result dates found.');
            return;
        }

        // Collect open date IDs
        $date_ids = $open_dates->pluck('id')->toArray();

        foreach ($permutations as $permutation) {
            $this->processWinningEntries($permutation, $date_ids);
        }

        //Log::info('CheckForThreeDWinnersWithPermutations job completed.');
    }

     protected function processWinningEntries($permutation, array $date_ids)
    {
        $today = Carbon::today(); // Current date
        $winningEntries = LotteryThreeDigitPivot::whereIn('result_date_id', $date_ids)
            ->where('bet_digit', $permutation)
            ->whereDate('created_at', $today)
            ->get();

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                try {
                    $lottery = Lotto::findOrFail($entry->lotto_id);
                    $user = $lottery->user;

                    $prize = $entry->sub_amount * 10; // Calculate the prize amount
                    $user->balance += $prize; // Update user balance
                    $user->save(); // Save updated user

                    $entry->prize_sent = 2; // Mark as prize sent
                    $entry->save();

                    Log::info("Prize awarded and prize_sent set to 2 for entry ID {$entry->id}.");
                } catch (\Exception $e) {
                    Log::error("Error during transaction for entry ID {$entry->id}: {$e->getMessage()}");
                    throw $e; // Ensure rollback if needed
                }
            });
        }
    }


    // protected function processWinningEntries($permutation)
    // {
    //     $today = Carbon::today();
    //     $open_date = ResultDate::where('status', 'open')
    //         ->get();
    //     // Correctly accumulate IDs into an array
    //     $dates = []; // Initialize an array
    //     foreach ($open_date as $date) {
    //         $dates[] = $date->id; // Add each ID to the array
    //     }

    //     Log::info('Open result date IDs:', ['dates' => $dates]);

    //     // Check if the $dates array is empty
    //     if (empty($dates) || ! is_array($dates)) {
    //         Log::warning('No open result dates found or $dates is not an array');
    //         return; // Exit the function if no valid open dates
    //     }

    //     $winningEntries = LotteryThreeDigitPivot::whereIn('result_date_id', $dates)
    //     ->where('bet_digit', $permutation)
    //     ->whereDate('created_at', $today)
    //     ->get();

    //     foreach ($winningEntries as $entry) {
    //         DB::transaction(function () use ($entry) {
    //             try {
    //                 $lottery = Lotto::findOrFail($entry->lotto_id);
    //                 $user = $lottery->user;

    //                 $prize = $entry->sub_amount * 10; // Calculate the prize amount
    //                 $user->balance += $prize; // Increase the user's balance
    //                 $user->save();

    //                 $entry->prize_sent = 2; // Mark as prize sent
    //                 $entry->save();

    //                 Log::info("Prize awarded and prize_sent set to true for entry ID {$entry->id}.");
    //             } catch (\Exception $e) {
    //                 Log::error("Error during transaction for entry ID {$entry->id}: ".$e->getMessage());
    //                 throw $e; // Trigger rollback if needed
    //             }
    //         });
    //     }
    // }

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