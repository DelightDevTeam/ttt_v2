<?php

namespace App\Models\TwoD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetIncome extends Model
{
    use HasFactory;

    protected $fillable = ['owner_balance', 'total_income', 'total_win_withdraw'];
}
