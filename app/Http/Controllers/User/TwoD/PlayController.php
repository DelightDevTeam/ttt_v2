<?php

namespace App\Http\Controllers\User\TwoD;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoDPlayRequest;
use App\Models\Admin\LotteryMatch;
use App\Models\Admin\RoleLimit;
use App\Models\TwoD\CloseTwoDigit;
use App\Models\TwoD\HeadDigit;
use App\Models\TwoD\Lottery;
use App\Models\TwoD\LotteryTwoDigitPivot;
use App\Models\TwoD\TwodGameResult;
use App\Models\TwoD\TwoDigit;
use App\Models\TwoD\TwoDLimit;
use App\Models\User;
use App\Services\TwoDService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayController extends Controller
{
    public function playindex()
    {
        return view('two_d.index');
    }

    public function index()
    {
        $twoDigits = TwoDigit::all();
        $limits = TwoDLimit::latest()->first()->two_d_limit;

        // Calculate remaining amounts for each two-digit
        $remainingAmounts = [];
        foreach ($twoDigits as $digit) {
            $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_pivot')
                ->where('two_digit_id', $digit->id)
                ->sum('sub_amount');
            $defaultLimitAmount = TwoDLimit::latest()->first()->two_d_limit;
            $remainingAmounts[$digit->id] = $defaultLimitAmount - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first();

        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:01:00' && $currentTime <= '12:01:00') {
            return view('two_d.12_pm.index', compact('twoDigits', 'remainingAmounts', 'lottery_matches', 'limits'));
        } elseif ($currentTime >= '12:01:00' && $currentTime <= '15:45:00') {
            return view('two_d.4_pm.index', compact('twoDigits', 'remainingAmounts', 'lottery_matches', 'limits'));
        } else {
            return 'closed'; // If outside known session times
        }

    }

    public function Quickindex()
    {
        $twoDigits = TwoDigit::all();
        $limits = TwoDLimit::latest()->first()->two_d_limit;

        // Calculate remaining amounts for each two-digit
        $remainingAmounts = [];
        foreach ($twoDigits as $digit) {
            $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_pivot')
                ->where('twod_game_result_id', $digit->id)
                ->sum('sub_amount');
            $defaultLimitAmount = TwoDLimit::latest()->first()->two_d_limit;
            $remainingAmounts[$digit->id] = $defaultLimitAmount - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first();

        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:01:00' && $currentTime <= '12:01:00') {
            return view('two_d.quick.index', compact('twoDigits', 'remainingAmounts', 'lottery_matches', 'limits'));
        } elseif ($currentTime >= '12:01:00' && $currentTime <= '15:45:00') {
            return view('two_d.quick.index', compact('twoDigits', 'remainingAmounts', 'lottery_matches', 'limits'));
        } else {
            return 'closed'; // If outside known session times
        }

    }

    public function play_confirm()
    {
        $twoDigits = TwoDigit::all();
        $limits = TwoDLimit::latest()->first()->two_d_limit;

        // Calculate remaining amounts for each two-digit
        $remainingAmounts = [];
        foreach ($twoDigits as $digit) {
            $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_pivot')
                ->where('two_digit_id', $digit->id)
                ->sum('sub_amount');
            $defaultLimitAmount = TwoDLimit::latest()->first()->two_d_limit;
            $remainingAmounts[$digit->id] = $defaultLimitAmount - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first();

        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:01:00' && $currentTime <= '12:01:00') {
            return view('two_d.12_pm.play_confirm', compact('twoDigits', 'remainingAmounts', 'lottery_matches', 'limits'));
        } elseif ($currentTime >= '12:01:00' && $currentTime <= '15:45:00') {
            return view('two_d.4_pm.play_confirm', compact('twoDigits', 'remainingAmounts', 'lottery_matches', 'limits'));
        } else {
            return 'closed'; // If outside known session times
        }

    }

    public function quick_play_confirm()
    {
        $twoDigits = TwoDigit::all();
        $limits = TwoDLimit::latest()->first()->two_d_limit;

        // Calculate remaining amounts for each two-digit
        $remainingAmounts = [];
        foreach ($twoDigits as $digit) {
            $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_pivot')
                ->where('two_digit_id', $digit->id)
                ->sum('sub_amount');
            $defaultLimitAmount = TwoDLimit::latest()->first()->two_d_limit;
            $remainingAmounts[$digit->id] = $defaultLimitAmount - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first();

        $currentTime = Carbon::now()->format('H:i:s');

        if ($currentTime >= '04:01:00' && $currentTime <= '12:01:00') {
            return view('two_d.quick.play_confirm', compact('twoDigits', 'remainingAmounts', 'lottery_matches', 'limits'));
        } elseif ($currentTime >= '12:01:00' && $currentTime <= '15:45:00') {
            return view('two_d.quick.play_confirm', compact('twoDigits', 'remainingAmounts', 'lottery_matches', 'limits'));
        } else {
            return 'closed'; // If outside known session times
        }
    }

    public function store(Request $request)
    {

        //Log::info($request->all());
        $validatedData = $request->validate([
            'selected_digits' => 'required|string',
            'amounts' => 'required|array',
            'amounts.*' => 'required|integer|min:1',
            'totalAmount' => 'required|numeric|min:1',
            'user_id' => 'required|exists:users,id',
        ]);

        $today = Carbon::now()->format('Y-m-d');

        $currentDate = TwodGameResult::where('result_date', $today)
            ->where('status', 'open')
            ->first();
        //return $currentDate->result_date . $currentDate->status;
        //dd($currentDate);

        // Check if the game is closed
        if (! $currentDate || $currentDate->status === 'closed') {
            // Set a flash message for the user
            session()->flash('error', '2D ပိတ်သွားပါပြီး ! နောက် session မှ ထပ်မံ ကံစမ်းပါ ၊ ကျေးဇူးတင်ပါတယ် ၊ The 2D lottery match is currently closed. Please come back later!');

            // Redirect back to the previous page
            return redirect()->back();
        }

        // Fetch all head digits not allowed
        $headDigitsNotAllowed = HeadDigit::query()
            ->get(['digit_one', 'digit_two', 'digit_three'])
            ->flatMap(function ($item) {
                return [$item->digit_one, $item->digit_two, $item->digit_three];
            })
            ->unique()
            ->all();

        // Check if any selected digit starts with the head digits not allowed
        foreach ($request->amounts as $two_digit_string => $sub_amount) {
            $headDigitOfSelected = substr($two_digit_string, 0, 1); // Extract the head digit
            if (in_array($headDigitOfSelected, $headDigitsNotAllowed)) {
                session()->flash('SuccessRequest', " ထိပ်ဂဏန်း - '{$headDigitOfSelected}' - ကိုပိတ်ထားသောကြောင့် ကံစမ်း၍ မရနိုင်ပါ ၊ ကျေးဇူးပြု၍ ဂဏန်းပြန်ရွှေးချယ်ပါ။");

                return redirect()->back();
            }
        }

        $closedDigits = CloseTwoDigit::all()->pluck('digit')->map(function ($digit) {
            return sprintf('%02d', $digit);
        })->toArray();

        // Iterate over submitted bets
        foreach ($request->input('amounts') as $bet => $amount) {
            $betDigit = sprintf('%02d', $bet); // Format the bet number

            // Check if the bet is on a closed digit
            if (in_array($betDigit, $closedDigits)) {
                session()->flash('SuccessRequest', "2D -'{$betDigit}' -ဂဏန်းကိုပိတ်ထားသောကြောင့် ကံစမ်း၍ မရနိုင်ပါ ၊ ကျေးဇူးပြု၍ ဂဏန်းပြန်ရွှေးချယ်ပါ။");

                return redirect()->back();
            }
        }

        $user = Auth::user();

        // Initialize default limit
        $defaultLimitAmount = TwoDLimit::latest()->first()->two_d_limit;

        // Adjust limit based on the user's role
        $userRole = $user->roles()->first();
        $roleLimitAmount = optional(RoleLimit::where('role_id', $userRole->id)->first())->limit ?? $defaultLimitAmount;
        $limitAmount = max($defaultLimitAmount, $roleLimitAmount);

        DB::beginTransaction();
        try {
            $user->decrement('balance', $request->totalAmount);
            $currentSession = $this->getCurrentSession();
            $currentDate = Carbon::now()->format('Y-m-d'); // Format the date and time as needed
            $currentTime = Carbon::now()->format('H:i:s');
            $customString = 'ttt-gaming-2d';
            $randomNumber = rand(10000000, 99999999); // Generate a random 4-digit number
            $slipNo = $randomNumber.'-'.$customString.'-'.$currentDate.'-'.$currentTime; // Combine date, string, and random number
            $lottery = Lottery::create([
                'pay_amount' => $request->totalAmount,
                'total_amount' => $request->totalAmount,
                'user_id' => $user->id,
                'slip_no' => $slipNo,
                'session' => $currentSession,
            ]);
            $totalAmount = $request->totalAmount; // The total amount from the request
            // Update the owner's balance (user with id = 1)
            $owner = User::find(1);
            $owner->balance += $totalAmount; // Add the total amount to the owner's balance
            $owner->save(); // Save the owner's new balance
            foreach ($request->amounts as $two_digit_string => $sub_amount) {
                $this->processBet($two_digit_string, $sub_amount, $limitAmount, $lottery);
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

    private function processBet($betDigit, $subAmount, $limitAmount, $lottery)
    {
        // Assuming $betDigit comes directly from the user input and represents the two-digit number they're betting on
        $twoDigit = TwoDigit::where('two_digit', sprintf('%02d', $betDigit))->first();

        if (! $twoDigit) {
            // Optionally handle the case where the two-digit number doesn't exist in the database
            throw new \Exception("Invalid bet digit: {$betDigit}");
        }

        $totalBetAmount = DB::table('lottery_two_digit_copy')->where('two_digit_id', $twoDigit->id)->sum('sub_amount');

        if ($totalBetAmount + $subAmount > $limitAmount) {
            throw new \Exception('သတ်မှတ်ဘရိတ်ကျော်လွန်နေသောကြောင့် ကံစမ်း၍မနိုင်တော့ပါ။ ကျေးဇူးတင်ပါတယ်!');
        }

        $user_ID = Auth::user();
        $to_day = Carbon::now()->format('Y-m-d');
        $currentSession = $this->getCurrentSession();
        $currentSessionTime = $this->getCurrentSessionTime();
        $results = TwodGameResult::where('result_date', $to_day)
            ->where('status', 'open')
            ->first();

        //return $results->$today . $results->status;

        LotteryTwoDigitPivot::create([
            'lottery_id' => $lottery->id,
            'twod_game_result_id' => $results->id,
            'two_digit_id' => $twoDigit->id,
            'user_id' => $user_ID->id,
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

    }
}