<?php

namespace App\Models\ThreeDigit;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryThreeDigitPivot extends Model
{
    use HasFactory;

    protected $table = 'lotto_three_digit_pivot';

    protected $fillable = ['result_date_id', 'lotto_id', 'three_digit_id', 'user_id', 'bet_digit', 'sub_amount', 'prize_sent', 'match_status', 'res_date', 'res_time', 'match_start_date', 'result_number', 'win_lose', 'admin_log', 'user_log'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // This will automatically boot with the model's events
    protected static function booted()
    {
        static::created(function ($pivot) {
            LotteryThreeDigitCopy::create($pivot->toArray());
        });
    }
}
