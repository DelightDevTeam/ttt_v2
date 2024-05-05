<?php

namespace App\Services;

use App\Models\TwoD\Lottery;
use App\Models\TwoD\LotteryTwoDigitPivot;
use App\Models\TwoD\TwodGameResult;
use App\Models\TwoD\TwoDLimit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwoDService
{
    public function play($totalAmount, array $amounts)
    {
        // Check for authentication
        if (! Auth::check()) {
            return 'User not authenticated';
        }

        $user = Auth::user();
        $break = TwoDLimit::latest()->first()->two_d_limit;

        if ($user->balance < $totalAmount) {
            return 'Insufficient funds.';
        }

        DB::beginTransaction(); // Begin transaction for data storage

        try {
            // Check for pre-process conditions
            $preOver = [];
            foreach ($amounts as $key => $amount) {
                if (! is_array($amount) || ! isset($amount['num']) || ! isset($amount['amount'])) {
                    Log::error('Invalid data format for amount: '.json_encode($amount));

                    continue; // Skip this iteration if invalid data
                }

                $preCheck = $this->preProcessAmountCheck($amount);
                if (is_array($preCheck)) {
                    $preOver[] = $preCheck[0];
                }
            }

            if (! empty($preOver)) {
                DB::rollback(); // Rollback the transaction

                return $preOver;
            }

            // Create a new lottery entry
            $lottery = Lottery::create([
                'pay_amount' => $totalAmount,
                'total_amount' => $totalAmount,
                'user_id' => $user->id,
            ]);

            // Process each amount
            $over = [];
            foreach ($amounts as $amount) {
                if (! is_array($amount) || ! isset($amount['num']) || ! isset($amount['amount'])) {
                    Log::error('Invalid data format for amount: '.json_encode($amount));

                    continue; // Skip this iteration if invalid data
                }

                $check = $this->processAmount($amount, $lottery->id);
                if (is_array($check)) {
                    $over[] = $check[0];
                }
            }

            if (! empty($over)) {
                DB::rollback(); // Rollback the transaction if over-limit

                return $over;
            }

            // Deduct the balance
            $user->balance -= $totalAmount;
            $user->save();

            // Commit the transaction
            DB::commit();

            return 'Bet placed successfully.';

        } catch (ModelNotFoundException $e) {
            DB::rollback(); // Rollback in case of error
            Log::error('Model not found in TwoDService play method: '.$e->getMessage());

            return 'Resource not found.';
        } catch (\Exception $e) {
            DB::rollback(); // Rollback in case of error
            Log::error('Error in TwoDService play method: '.$e->getMessage());

            return 'An error occurred while placing the bet. Please try again later.'; // Handle general exceptions
        }
    }

    protected function getCurrentSession()
    {
        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:01:00' && $currentTime <= '12:01:00') {
            return 'morning';
        } elseif ($currentTime >= '12:01:00' && $currentTime <= '15:45:00') {
            return 'evening';
        } else {
            return 'closed'; // If outside known session times
        }
    }

    protected function getCurrentSessionTime()
    {
        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:01:00' && $currentTime <= '12:01:00') {
            return '12:01:00';
        } elseif ($currentTime >= '12:01:00' && $currentTime <= '15:45:00') {
            return '16:30:00';
        } else {
            return 'closed'; // If outside known session times
        }
    }

    protected function preProcessAmountCheck($amount)
    {
        if (! is_array($amount) || ! isset($amount['num']) || ! isset($amount['amount'])) {
            Log::error('Invalid data format for amount: '.json_encode($amount));

            return; // Return null or handle the error as needed
        }

        $twoDigit = str_pad($amount['num'], 2, '0', STR_PAD_LEFT); // Ensure two-digit format
        $break = TwoDLimit::latest()->first()->two_d_limit;

        Log::info("User's commission limit: {$break}");
        Log::info("Checking bet_digit: {$twoDigit}");

        // Get the total bet amount for the given two-digit
        $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_pivot')
            ->where('bet_digit', $twoDigit)
            ->sum('sub_amount');

        Log::info("Total bet amount for {$twoDigit}: {$totalBetAmountForTwoDigit}");

        $subAmount = $amount['amount'];

        // Check if the total bet exceeds the break limit
        if ($totalBetAmountForTwoDigit + $subAmount > $break) {
            Log::warning("Bet on {$twoDigit} exceeds limit.");

            return [$amount['num']]; // Indicates over-limit
        }

        // Indicates no over-limit
    }

    protected function processAmount($amount, $lotteryId)
    {
        if (! is_array($amount) || ! isset($amount['num']) || ! isset($amount['amount'])) {
            Log::error('Invalid data format for amount: '.json_encode($amount));

            return; // Return null or handle the error as needed
        }

        $twoDigit = str_pad($amount['num'], 2, '0', STR_PAD_LEFT); // Ensure two-digit format
        $break = TwoDLimit::latest()->first()->two_d_limit;

        $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_pivot')
            ->where('bet_digit', $twoDigit)
            ->sum('sub_amount');

        $subAmount = $amount['amount'];
        $betDigit = $amount['num'];

        if ($totalBetAmountForTwoDigit + $subAmount <= $break) {
            $user = Auth::user();
            $today = Carbon::now()->format('Y-m-d');
            $currentSession = $this->getCurrentSession();
            $currentSessionTime = $this->getCurrentSessionTime();

            // Retrieve results for today where status is 'open'
            $results = TwodGameResult::where('result_date', $today)
                ->where('status', 'open')
                ->first();

            if ($results) { // Check if results are valid
                LotteryTwoDigitPivot::create([
                    'lottery_id' => $lotteryId,
                    'twod_game_result_id' => $results->id,
                    'user_id' => $user->id,
                    'bet_digit' => $betDigit,
                    'sub_amount' => $subAmount,
                    'prize_sent' => false,
                    'match_status' => $results->status,
                    'res_date' => $results->result_date,
                    'res_time' => $currentSessionTime,
                    'session' => $currentSession,
                    'admin_log' => $results->admin_log,
                    'user_log' => $results->user_log,
                ]);
            } else {
                Log::error("No open TwodGameResult found for today's date.");

                return; // Handle missing results
            }
        } else {
            // Handle over-limit cases
            return [$betDigit]; // Return the digit that exceeded the limit
        }
    }

    private function determineSession()
    {
        return date('H') < 12 ? 'morning' : 'evening';
    }
}
