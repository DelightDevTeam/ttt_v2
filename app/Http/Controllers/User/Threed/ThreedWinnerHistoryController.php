<?php

namespace App\Http\Controllers\User\Threed;

use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\Lotto;
use App\Models\ThreeDigit\ThreeWinner;
use Illuminate\Http\Request;

class ThreedWinnerHistoryController extends Controller
{
    public function index()
    {
        $winners = Lotto::whereHas('threedDigits', function ($query) {
            $query->where('prize_sent', 1);
        })->with(['threedDigits' => function ($query) {
            $query->where('prize_sent', 1);
        }])->get();
        $three_digits_prize = ThreeWinner::orderBy('id', 'desc')->first();

        return view('three_d.threed-winner-history', compact('winners', 'three_digits_prize'));
        //return response()->json($winners);
    }
}
