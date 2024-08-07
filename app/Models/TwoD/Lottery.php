<?php

namespace App\Models\TwoD;

use App\Models\Admin\LotteryMatch;
use App\Models\Admin\PrizeSentTwoDigit;
use App\Models\TwoD\TwoDigit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    use HasFactory;

    protected $fillable = [
        'pay_amount',
        'total_amount',
        'user_id',
        'session',
        'lottery_match_id',
        'slip_no',
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

    public function twoDigits()
    {
        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivot')->withPivot('sub_amount', 'prize_sent')->withTimestamps();
    }

    public function twoDigitLimits()
    {
        return $this->belongsToMany(TwoDigit::class, 'lottery_over_limit_copy')->withPivot('sub_amount', 'prize_sent')->withTimestamps();
    }

    // two digit early morning
    public function twoDigitsEarlyMorning()
    {
        $morningStart = Carbon::now()->startOfDay()->addHours(6);
        $morningEnd = Carbon::now()->startOfDay()->addHours(1);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_copy', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    // two digit morning 9: 30 am over amount limit
    public function twoDigitsOverAmountEarlyMorning()
    {
        $morningStart = Carbon::now()->startOfDay()->addHours(6);
        $morningEnd = Carbon::now()->startOfDay()->addHours(10);

        return $this->belongsToMany(TwoDigit::class, 'lottery_over_limit_copy', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    public function twoDigitsMorning()
    {
        $morningStart = Carbon::now()->startOfDay()->addHours(6);
        $morningEnd = Carbon::now()->startOfDay()->addHours(12);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivot', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    // two digit morning 12: 1 am over amount limit
    public function twoDigitsMorningOverLimit()
    {
        $morningStart = Carbon::now()->startOfDay()->addHours(10);
        $morningEnd = Carbon::now()->startOfDay()->addHours(12);

        return $this->belongsToMany(TwoDigit::class, 'lottery_over_limit_copy', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    // two digit morning 2: pm over amount limit
    public function twoDigitsEarlyEvenOverLimit()
    {
        $morningStart = Carbon::now()->startOfDay()->addHours(12);
        $morningEnd = Carbon::now()->startOfDay()->addHours(14);

        return $this->belongsToMany(TwoDigit::class, 'lottery_over_limit_copy', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    // two digit morning 4:30 pm over amount limit
    public function twoDigitsEveningOverLimit()
    {
        $morningStart = Carbon::now()->startOfDay()->addHours(14);
        $morningEnd = Carbon::now()->startOfDay()->addHours(24);

        return $this->belongsToMany(TwoDigit::class, 'lottery_over_limit_copy', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    // two digit early evening
    public function twoDigitsEarlyEvening()
    {
        $eveningStart = Carbon::now()->startOfDay()->addHours(12);
        $eveningEnd = Carbon::now()->startOfDay()->addHours(14);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivot', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$eveningStart, $eveningEnd]);
    }

    public function twoDigitsEvening()
    {
        $eveningStart = Carbon::now()->startOfDay()->addHours(12);
        $eveningEnd = Carbon::now()->startOfDay()->addHours(24);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivot', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$eveningStart, $eveningEnd]);
    }

    // public function twoDigitsMorning()
    // {
    //     $morningStart = Carbon::now()->startOfDay()->addHours(6);
    //     $morningEnd = Carbon::now()->startOfDay()->addHours(12);
    //     return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivot', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
    //                 ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    // }

    // public function twoDigitsEvening()
    // {
    //     $eveningStart = Carbon::now()->startOfDay()->addHours(12);
    //     $eveningEnd = Carbon::now()->startOfDay()->addHours(24);
    //     return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivot', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
    //                 ->wherePivotBetween('created_at', [$eveningStart, $eveningEnd]);
    // }
}
