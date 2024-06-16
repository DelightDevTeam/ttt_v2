<?php

namespace App\Models\ThreeDigit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryThreeDigitCopy extends Model
{
    use HasFactory;

    protected $table = 'lotto_three_digit_copy';

    protected $fillable = ['result_date_id', 'lotto_id', 'three_digit_id', 'user_id', 'bet_digit', 'sub_amount', 'prize_sent', 'match_status', 'res_date', 'res_time', 'match_start_date', 'result_number', 'win_lose', 'admin_log', 'user_log'];
}
