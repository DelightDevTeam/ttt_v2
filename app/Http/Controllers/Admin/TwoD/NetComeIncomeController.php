<?php

namespace App\Http\Controllers\Admin\TwoD;

use App\Http\Controllers\Controller;
use App\Models\TwoD\NetIncome;
use Illuminate\Http\Request;

class NetComeIncomeController extends Controller
{
    public function updateNetIncome(Request $request)
    {
        // Fetch the first (or the specific) record from the NetIncome table
        $netIncome = NetIncome::firstOrCreate(['id' => 1]); // This example assumes there's only one record

        // Update the values from the request
        $netIncome->total_income = $request->input('total_income');
        //$netIncome->total_win_withdraw = $request->input('total_win_withdraw');

        // Save the changes
        $netIncome->save();

        return redirect()->back()->with('success', 'Net income updated successfully!');
    }

    public function updateWinWithdraw(Request $request)
    {
        // Fetch the first (or the specific) record from the NetIncome table
        $netIncome = NetIncome::firstOrCreate(['id' => 1]); // This example assumes there's only one record

        // Update the values from the request
        //$netIncome->total_income = $request->input('total_income');
        $netIncome->total_win_withdraw = $request->input('total_win_withdraw');

        // Save the changes
        $netIncome->save();

        return redirect()->back()->with('success', 'Net income updated successfully!');
    }
}
