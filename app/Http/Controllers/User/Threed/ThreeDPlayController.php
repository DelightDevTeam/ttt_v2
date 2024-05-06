<?php

namespace App\Http\Controllers\User\Threed;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThreedPlayRequest;
use App\Models\Admin\LotteryMatch;
use App\Models\Admin\ThreeDDLimit;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Lotto;
use App\Models\ThreeDigit\ResultDate;
use App\Models\ThreeDigit\ThreedClose;
use App\Models\ThreeDigit\ThreeDigit;
use App\Models\ThreeDigit\ThreeDigitOverLimit;
use App\Models\ThreeDigit\ThreeDLimit;
use App\Models\User;
use App\Services\LottoService;
use App\Services\LottoSessionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThreeDPlayController extends Controller
{
    protected $lottoService;

    protected $lottoSessionService;

    public function __construct(LottoService $lottoService, LottoSessionService $lottoSessionService)
    {
        $this->lottoService = $lottoService;
        $this->lottoSessionService = $lottoSessionService;
    }

    public function getLottoDataForCurrentMonth()
    {
        // Retrieve data using the service
        $data = $this->lottoSessionService->getThreeDigitData();

        // Return a view and pass the data to it
        return view('three_d.three_d_display', ['displayThreeDigits' => $data]);
    }

    public function index()
    {
        return view('three_d.index');
    }

    // threed play
    public function choiceplay()
    {
        $threeDigits = ThreeDigit::all();

        // Calculate remaining amounts for each two-digit
        $remainingAmounts = [];
        foreach ($threeDigits as $digit) {
            $totalBetAmountForTwoDigit = DB::table('lotto_three_digit_pivot')
                ->where('three_digit_id', $digit->id)
                ->sum('sub_amount');

            $remainingAmounts[$digit->id] = 50000 - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 2)->whereNotNull('is_active')->first();

        return view('three_d.three_d_choice_play', compact('threeDigits', 'remainingAmounts', 'lottery_matches'));
        //return view('three_d.three_d_choice_play');
    }

    public function confirm_play()
    {
        $threeDigits = ThreeDigit::all();

        // Calculate remaining amounts for each two-digit
        $remainingAmounts = [];
        foreach ($threeDigits as $digit) {
            $totalBetAmountForTwoDigit = DB::table('lotto_three_digit_pivot')
                ->where('three_digit_id', $digit->id)
                ->sum('sub_amount');

            $remainingAmounts[$digit->id] = 50000 - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 2)->whereNotNull('is_active')->first();

        return view('three_d.play_confirm', compact('threeDigits', 'remainingAmounts', 'lottery_matches'));
        //return view('three_d.three_d_choice_play');
    }

    public function user_play()
    {
        $userId = auth()->id(); // Get logged in user's ID

        $displayThreeDigits = User::getUserThreeDigits($userId);

        return view('three_d.three_d_display', [
            'displayThreeDigits' => $displayThreeDigits,
        ]);
    }

    public function store(Request $request)
    {
        //Log::info("Store method called with request data:", ['data' => $request->all()]);

        // Validate the request data
        $validatedData = $request->validate([
            'selected_digits' => 'required|string',
            'amounts' => 'required|array',
            'amounts.*' => 'required|integer|min:100',
            'totalAmount' => 'required|numeric|min:100',
            'user_id' => 'required|exists:users,id',
        ]);

        $open_date = ResultDate::where('status', 'open')->first();
        if (! $open_date) {
            session()->flash('error', '3D ပိတ်သွားပါပြီး ! နောက် session မှ ထပ်မံ ကံစမ်းပါ ၊ ကျေးဇူးတင်ပါတယ် ၊ The 3D lottery match is currently closed. Please come back later!');

            // Redirect back to the previous page
            return redirect()->back();
        }

        // Get the current limit amount
        $limitAmount = ThreeDLimit::latest()->first()->three_d_limit;
        Log::info('Current limit amount:', ['limit' => $limitAmount]);

        // Get closed digits
        $closedDigits = ThreedClose::pluck('digit')->map(function ($digit) {
            return sprintf('%03d', $digit); // Ensure two-digit format
        })->unique()->filter()->values()->all();

        // Check for closed digits in the request
        foreach ($request->input('amounts') as $three_digit_string => $sub_amount) {
            $three_digit_formatted = sprintf('%03d', $three_digit_string);

            if (in_array($three_digit_formatted, $closedDigits)) {
                return redirect()->back()->with('error', "3D digit '{$three_digit_formatted}' is closed. Please select a different digit.");
            }
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Check user balance
            if ($user->balance < $request->totalAmount) {
                throw new Exception('Insufficient balance.');
            }

            $user->balance -= $request->totalAmount;
            $user->save();

            // Create a new lottery record
            $lottery = Lotto::create([
                'total_amount' => $request->totalAmount,
                'user_id' => $request->user_id,
            ]);

            // Handle the amounts and check against the limit
            foreach ($request->amounts as $three_digit_string => $sub_amount) {
                $three_digit_formatted = sprintf('%03d', $three_digit_string);

                // Get the total bet amount for this digit
                $totalBetAmountForDigit = DB::table('lotto_three_digit_pivot')
                    ->where('three_digit_id', intval($three_digit_formatted))
                    ->sum('sub_amount');

                if ($totalBetAmountForDigit + $sub_amount > $limitAmount) {
                    throw new Exception("Bet for '{$three_digit_formatted}' exceeds the limit.");
                }

                // Create a new pivot for within the limit
                $result = ResultDate::where('status', 'open')->first();
                if (! $result) {
                    throw new Exception('No open ResultDate found.');
                }
                $user_id = Auth::user();
                $result = ResultDate::where('status', 'open')->first();
                if (! $user_id) {
                    throw new Exception('No user found.');
                }
                $open_date = ResultDate::where('status', 'open')
                    ->get();

                $pivot = new LotteryThreeDigitPivot([
                    'result_date_id' => $result->id,
                    'lotto_id' => $lottery->id,
                    'three_digit_id' => intval($three_digit_formatted),
                    'user_id' => $user_id->id,
                    'bet_digit' => $three_digit_formatted,
                    'sub_amount' => $sub_amount,
                    'prize_sent' => false,
                    'match_status' => $result->status,
                    'res_date' => $result->result_date,
                    'res_time' => $result->result_time,
                    'match_start_date' => $result->match_start_date,
                    'admin_log' => $result->admin_log,
                    'user_log' => $result->user_log,
                ]);
                $pivot->save();
            }

            DB::commit();

            session()->flash('SuccessRequest', 'Successfully placed bet.');

            return redirect()->route('user.display')->with('message', 'Data stored successfully!');
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error in store method:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', "Error: {$e->getMessage()}");
        }
    }
    // public function store(Request $request)
    // {

    //     Log::info($request->all());
    //     $validatedData = $request->validate([
    //         'selected_digits' => 'required|string',
    //         'amounts' => 'required|array',
    //         //'amounts.*.num' => 'required|integer',
    //         'amounts.*' => 'required|integer|min:100',
    //         'totalAmount' => 'required|numeric|min:100',
    //         'user_id' => 'required|exists:users,id',
    //     ]);

    //     //$currentSession = date('H') < 12 ? 'morning' : 'evening';
    //     //$limitAmount = 50000; // Define the limit amount
    //     $limitAmount = ThreeDLimit::latest()->first()->three_d_limit;
    //     Log::info($limitAmount);

    //     $closedTwoDigits = ThreedClose::query()
    //         ->pluck('digit')
    //         ->map(function ($digit) {
    //             // Ensure formatting as a two-digit string
    //             return sprintf('%03d', $digit);
    //         })
    //         ->unique()
    //         ->filter()
    //         ->values()
    //         ->all();

    //     foreach ($request->input('amounts') as $three_digit_string => $sub_amount) {
    //         $twoDigitOfSelected = sprintf('%03d', $three_digit_string); // Format the key as a three-digit string

    //         if (in_array($twoDigitOfSelected, $closedTwoDigits)) {
    //             return redirect()->back()->with('error', "3D - '{$twoDigitOfSelected}' ဂဏန်းကိုပိတ်ထားပါသည်။ သင့်ကံစမ်းမှုမအောင်မြင်ပါ - ကျေးဇူးပြု၍ ဂဏန်းပြန်ရွှေးချယ်ပါ။");
    //         }
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $user = Auth::user();
    //         $user->balance -= $request->totalAmount;

    //         if ($user->balance < 0) {
    //             throw new \Exception('Insufficient balance.');
    //         }

    //         $user->save();

    //         $lottery = Lotto::create([
    //             //'pay_amount' => $request->totalAmount,
    //             'total_amount' => $request->totalAmount,
    //             'user_id' => $request->user_id,
    //             //'session' => $currentSession
    //         ]);

    //         foreach ($request->amounts as $three_digit_string => $sub_amount) {
    //             $three_digit_id = $three_digit_string === '00' ? 1 : intval($three_digit_string, 10) + 1;

    //             $totalBetAmountForTwoDigit = DB::table('lotto_three_digit_pivot')
    //                 ->where('three_digit_id', $three_digit_id)
    //                 ->sum('sub_amount');

    //             if ($totalBetAmountForTwoDigit + $sub_amount <= $limitAmount) {
    //                 $pivot = new LotteryThreeDigitPivot([
    //                     'lotto_id' => $lottery->id,
    //                     'three_digit_id' => $three_digit_id,
    //                     'bet_digit' => $three_digit_string,
    //                     'sub_amount' => $sub_amount,
    //                     'prize_sent' => false,
    //                 ]);
    //                 $pivot->save();
    //             } else {
    //                 $withinLimit = $limitAmount - $totalBetAmountForTwoDigit;
    //                 $overLimit = $sub_amount - $withinLimit;

    //                 if ($withinLimit > 0) {
    //                     $playerID = Auth::user();
    //                     $results = ResultDate::where('status', 'open')->first();
    //                     $pivotWithin = new LotteryThreeDigitPivot([
    //                         'result_date_id' => $results->id,
    //                         'lotto_id' => $lottery->id,
    //                         'three_digit_id' => $three_digit_id,
    //                         'user_id' => $playerID->id,
    //                         'bet_digit' => $three_digit_string,
    //                         'sub_amount' => $withinLimit,
    //                         'prize_sent' => false,
    //                         'match_status' => $results->status,
    //                         'res_date' => $results->result_date,
    //                         'match_start_date' => $results->match_start_date,
    //                         'admin_log' => $results->admin_log,
    //                         'user_log' => $results->user_log
    //                     ]);
    //                     $pivotWithin->save();
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         session()->flash('SuccessRequest', 'Successfully placed bet.');

    //         return redirect()->route('user.display')->with('message', 'Data stored successfully!');
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error('Error in store method: '.$e->getMessage());

    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }
}
