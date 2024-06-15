<?php

namespace App\Models\ThreeDigit;

use App\Models\Admin\LotteryMatch;
// use App\Models\Admin\ThreedDigit;
use App\Models\Admin\ThreedMatchTime;
use App\Models\ThreeDigit\ThreeDigit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Lotto extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_amount',
        'user_id',
        //'session',
        'lottery_match_id',
        'slip_no'

    ];

    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lotteryMatch()
    {
        return $this->belongsTo(LotteryMatch::class, 'lottery_match_id');
    }

    public function threedMatchTime()
    {
        // Assuming you have a model called ThreedMatchTime and there is a 'lottery_match_id' foreign key in it.
        return $this->hasOne(ThreedMatchTime::class, 'id', 'lottery_match_id');
    }

    public function threedDigits()
    {
        return $this->belongsToMany(ThreeDigit::class, 'lotto_three_digit_pivot')->withPivot('sub_amount', 'prize_sent')->withTimestamps();
    }

    // public function DisplayThreeDigits()
    // {

    // return $this->belongsToMany(ThreeDigit::class, 'lotto_three_digit_pivot', 'lotto_id', 'three_digit_id')->withPivot('bet_digit', 'sub_amount', 'prize_sent', 'match_status', 'res_date', 'res_time', 'match_start_date', 'user_log', 'created_at');
    // }
    //     public function DisplayThreeDigits()
    // {
    //     $currentMonth = Carbon::now()->month;
    //     $currentYear = Carbon::now()->year;

    //     // Define the date range for the sessions
    //     $firstSessionStart = Carbon::create($currentYear, $currentMonth, 2);
    //     $firstSessionEnd = Carbon::create($currentYear, $currentMonth, 16);

    //     $secondSessionStart = Carbon::create($currentYear, $currentMonth, 17);
    //     $secondSessionEnd = Carbon::create($currentYear, $currentMonth + 1, 1); // 1st of the next month

    //     // Special case for May
    //     if ($currentMonth == 5) {
    //         $firstSessionStart = Carbon::create($currentYear, 4, 17); // Start from the 17th of the previous month
    //         $firstSessionEnd = Carbon::create($currentYear, 5, 2); // End on the 2nd of May

    //         $secondSessionStart = Carbon::create($currentYear, 5, 17); // Start on the 17th of May
    //         $secondSessionEnd = Carbon::create($currentYear, 6, 1); // End on the 1st of June
    //     }

    //     // Query the related ThreeDigit records based on the defined session ranges
    //     return $this->belongsToMany(ThreeDigit::class, 'lotto_three_digit_pivot', 'lotto_id', 'three_digit_id')
    //         ->withPivot('bet_digit', 'sub_amount', 'prize_sent', 'match_status', 'res_date', 'res_time', 'match_start_date', 'user_log', 'created_at')
    //         ->whereBetween('lotto_three_digit_pivot.res_date', [$firstSessionStart, $firstSessionEnd]) // First session
    //         ->orWhereBetween('lotto_three_digit_pivot.res_date', [$secondSessionStart, $secondSessionEnd]); // Second session

    // }

    public function DisplayThreeDigits()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Define the date range for the sessions
        $firstSessionStart = Carbon::create($currentYear, $currentMonth, 2);
        $firstSessionEnd = Carbon::create($currentYear, $currentMonth, 16);

        $secondSessionStart = Carbon::create($currentYear, $currentMonth, 17);
        $secondSessionEnd = Carbon::create($currentYear, $currentMonth + 1, 1); // 1st of the next month

        // Special case for May
        if ($currentMonth == 5) {
            $firstSessionStart = Carbon::create($currentYear, 4, 17); // 17th of the previous month
            $firstSessionEnd = Carbon::create($currentYear, 5, 2);    // 2nd of May

            $secondSessionStart = Carbon::create($currentYear, 5, 17); // 17th of May
            $secondSessionEnd = Carbon::create($currentYear, 6, 1);    // 1st of June
        }

        // Get the authenticated user's ID
        $userId = auth()->id();

        // Fetch the related ThreeDigit records for the current user, based on the defined session ranges
        $threeDigitData = $this->belongsToMany(ThreeDigit::class, 'lotto_three_digit_pivot', 'lotto_id', 'three_digit_id')
            ->withPivot('bet_digit', 'sub_amount', 'prize_sent', 'match_status', 'res_date', 'res_time', 'match_start_date', 'user_log', 'created_at')
            ->where('lotto_three_digit_pivot.user_id', $userId) // Filter by user ID
            ->whereBetween('lotto_three_digit_pivot.res_date', [$firstSessionStart, $firstSessionEnd]) // First session
            ->orWhereBetween('lotto_three_digit_pivot.res_date', [$secondSessionStart, $secondSessionEnd]) // Second session
            ->get(); // Fetch the results

        // Calculate the total sub_amount for all retrieved records
        $totalAmount = $threeDigitData->sum(function ($item) {
            return $item->pivot->sub_amount; // Sum the sub_amount for each related pivot record
        });

        return [
            'threeDigit' => $threeDigitData, // The fetched records
            'total_amount' => $totalAmount, // The calculated total sub_amount
        ];
    }
}
