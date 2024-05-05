<?php

namespace App\Http\Controllers\User\PM12;

use App\Http\Controllers\Controller;
use App\Models\Admin\LotteryMatch;
use App\Models\Admin\RoleLimit;
use App\Models\TwoD\CloseTwoDigit;
use App\Models\TwoD\HeadDigit;
use App\Models\TwoD\Lottery;
use App\Models\TwoD\LotteryTwoDigitPivot;
use App\Models\TwoD\TwoDigit;
use App\Models\TwoD\TwoDLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwodPlay12PMController extends Controller
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
            $defaultLimitAmount = TwoDLimit::latest()->first()->two_d_limit;

            $remainingAmounts[$digit->id] = $defaultLimitAmount - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first();

        return view('two_d.12_pm.index', compact('twoDigits', 'remainingAmounts', 'lottery_matches'));
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
            $defaultLimitAmount = TwoDLimit::latest()->first()->two_d_limit;

            $remainingAmounts[$digit->id] = $defaultLimitAmount - $totalBetAmountForTwoDigit; // Assuming 5000 is the session limit
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first();

        return view('two_d.12_pm.play_confirm', compact('twoDigits', 'remainingAmounts', 'lottery_matches'));
    }
}
