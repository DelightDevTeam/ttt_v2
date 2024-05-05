<?php

namespace App\Services;

use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Lotto;
use App\Models\ThreeDigit\ResultDate;
use App\Models\ThreeDigit\ThreeDLimit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LottoService
{
    public function play($totalAmount, $amounts)
    {
        // Begin Transaction
        DB::beginTransaction();

        try {
            $user = Auth::user();

            if ($user->balance < $totalAmount) {
                // throw new \Exception('Insufficient balance.');
                return 'Insufficient funds.';
            }

            $preOver = [];
            foreach ($amounts as $amount) {
                $preCheck = $this->preProcessAmountCheck($amount);
                if (is_array($preCheck)) {
                    $preOver[] = $preCheck[0];
                }
            }
            if (! empty($preOver)) {
                return $preOver;
            }

            //$lottery = $this->createLottery($totalAmount, $user->id);
            $lottery = Lotto::create([
                'total_amount' => $totalAmount,
                'user_id' => $user->id,
            ]);

            $over = [];
            foreach ($amounts as $amount) {
                $check = $this->processAmount($amount, $lottery->id);
                if (is_array($check)) {
                    $over[] = $check[0];
                }
            }
            if (! empty($over)) {
                return $over;
            }

            $user->decrement('balance', $totalAmount);

            DB::commit();

            // return $lottery;
        } catch (\Exception $e) {
            DB::rollback();

            //throw $e;
            return response()->json(['message' => $e->getMessage()], 401);
            //  return $e->getMessage();
        }
    }

    protected function preProcessAmountCheck($item)
    {
        $num = str_pad($item['num'], 3, '0', STR_PAD_LEFT); // Ensure three-digit format
        $sub_amount = $item['amount'];

        $totalBetAmount = DB::table('lotto_three_digit_pivot')
            ->where('bet_digit', $num)
            ->sum('sub_amount');

        $break = ThreeDLimit::latest()->first()->three_d_limit;

        if ($totalBetAmount + $sub_amount > $break) {
            // throw new \Exception("The bet amount for number $num exceeds the limit.");
            return [$item['num']];
        }
    }

    protected function processAmount($item, $lotteryId)
    {
        $num = str_pad($item['num'], 3, '0', STR_PAD_LEFT); // Ensure three-digit format
        $sub_amount = $item['amount'];

        $totalBetAmount = DB::table('lotto_three_digit_pivot')
            ->where('bet_digit', $num)
            ->sum('sub_amount');

        $break = ThreeDLimit::latest()->first()->three_d_limit;

        if ($totalBetAmount + $sub_amount <= $break) {
            $userID = Auth::user();
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();

            $results = ResultDate::where('status', 'open')
                ->whereBetween('result_date', [$currentMonthStart, $currentMonthEnd])
                ->first();

            if ($results->status == 'closed') {
                return response()->json(['message' => '3D game does not open for this time']);
            }
            // Insert into the pivot table
            $pivot = new LotteryThreeDigitPivot([
                'lotto_id' => $lotteryId,
                'result_date_id' => $results->id,
                'user_id' => $userID->id,
                'bet_digit' => $num,
                'sub_amount' => $sub_amount,
                'prize_sent' => false,
                'match_status' => $results->status,
                'res_date' => $results->result_date,
                'res_time' => $results->result_time,
                'match_start_date' => $results->match_start_date,
                'admin_log' => $results->admin_log,
                'user_log' => $results->user_log,
            ]);

            $pivot->save();
        } else {
            return [$item['num']];
            throw new \Exception('The bet amount exceeds the limit.');

            return response()->json(['message' => 'သတ်မှတ်ထားသော limit ပမာဏထပ်ကျော်လွန်နေပါသည်။'], 401);
        }
    }
}
