<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LotteryOverLimitCopy;
use App\Models\TwoD\LotteryTwoDigitCopy;
use Illuminate\Http\Request;

class SessionResetControlller extends Controller
{
    public function index()
    {

        return view('admin.two_d.session_reset');
    }

    public function SessionReset()
    {
        LotteryTwoDigitCopy::truncate();
        session()->flash('SuccessRequest', 'Successfully 2D Session Reset.');

        return redirect()->back()->with('message', 'Data reset successfully!');
    }
}
