<?php

namespace App\Models\TwoD;

use Illuminate\Support\Lottery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TwoDigit extends Model
{
    use HasFactory;

    protected $fillable = [
        'two_digit',
    ];

    public function lotteries()
    {
        return $this->belongsToMany(Lottery::class, 'lottery_two_digit_pivot')->withPivot('sub_amount');
    }
}