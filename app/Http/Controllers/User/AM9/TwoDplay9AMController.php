<?php

namespace App\Http\Controllers\User\AM9;

use App\Http\Controllers\Controller;
use App\Models\Admin\LotteryMatch;
use App\Models\Admin\TwoDigit;
use App\Models\Lottery;
use App\Models\LotteryTwoDigitOverLimit;
use App\Models\LotteryTwoDigitPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwoDplay9AMController extends Controller
{
    public function index()
    {
        $twoDigits = TwoDigit::all();

        // Calculate remaining amounts for each two-digit
        $remainingAmounts = [];
        foreach ($twoDigits as $digit) {
            $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_copy')
                ->where('two_digit_id', $digit->id)
                ->sum('sub_amount');

            $remainingAmounts[$digit->id] = 50000 - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first();

        return view('two_d.9_am.index', compact('twoDigits', 'remainingAmounts', 'lottery_matches'));
    }

    public function play_confirm()
    {
        $twoDigits = TwoDigit::all();

        // Calculate remaining amounts for each two-digit
        $remainingAmounts = [];
        foreach ($twoDigits as $digit) {
            $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_copy')
                ->where('two_digit_id', $digit->id)
                ->sum('sub_amount');

            $remainingAmounts[$digit->id] = 50000 - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first();

        return view('two_d.9_am.play_confirm', compact('twoDigits', 'remainingAmounts', 'lottery_matches'));
    }

    public function store(Request $request)
    {

        Log::info($request->all());
        $validatedData = $request->validate([
            'selected_digits' => 'required|string',
            'amounts' => 'required|array',
            'amounts.*' => 'required|integer|min:100|max:50000',
            //'totalAmount' => 'required|integer|min:100',
            'totalAmount' => 'required|numeric|min:100', // Changed from integer to numeric
            'user_id' => 'required|exists:users,id',
        ]);

        $currentSession = date('H') < 12 ? 'morning' : 'evening';
        $limitAmount = 50000; // Define the limit amount

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $user->balance -= $request->totalAmount;

            if ($user->balance < 0) {
                throw new \Exception('Insufficient balance.');
            }

            $user->save();

            $lottery = Lottery::create([
                'pay_amount' => $request->totalAmount,
                'total_amount' => $request->totalAmount,
                'user_id' => $request->user_id,
                'session' => $currentSession,
            ]);

            foreach ($request->amounts as $two_digit_string => $sub_amount) {
                $two_digit_id = $two_digit_string === '00' ? 1 : intval($two_digit_string, 10) + 1;

                $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_copy')
                    ->where('two_digit_id', $two_digit_id)
                    ->sum('sub_amount');

                if ($totalBetAmountForTwoDigit + $sub_amount <= $limitAmount) {
                    $pivot = new LotteryTwoDigitPivot([
                        'lottery_id' => $lottery->id,
                        'two_digit_id' => $two_digit_id,
                        'sub_amount' => $sub_amount,
                        'prize_sent' => false,
                    ]);
                    $pivot->save();
                } else {
                    $withinLimit = $limitAmount - $totalBetAmountForTwoDigit;
                    $overLimit = $sub_amount - $withinLimit;

                    if ($withinLimit > 0) {
                        $pivotWithin = new LotteryTwoDigitPivot([
                            'lottery_id' => $lottery->id,
                            'two_digit_id' => $two_digit_id,
                            'sub_amount' => $withinLimit,
                            'prize_sent' => false,
                        ]);
                        $pivotWithin->save();
                    }

                    if ($overLimit > 0) {
                        $pivotOver = new LotteryTwoDigitOverLimit([
                            'lottery_id' => $lottery->id,
                            'two_digit_id' => $two_digit_id,
                            'sub_amount' => $overLimit,
                            'prize_sent' => false,
                        ]);
                        $pivotOver->save();
                    }
                }
            }

            DB::commit();
            session()->flash('SuccessRequest', 'Successfully placed bet.');

            return redirect()->route('home')->with('message', 'Data stored successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in store method: '.$e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
