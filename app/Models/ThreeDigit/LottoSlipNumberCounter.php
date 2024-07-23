<?php

namespace App\Models\ThreeDigit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LottoSlipNumberCounter extends Model
{
    use HasFactory;
    protected $fillable = ['current_number'];

}
