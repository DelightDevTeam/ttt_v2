<?php

namespace App\Models\ThreeDigit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreeDLimit extends Model
{
    use HasFactory;

    protected $table = 'three_d_limits';
    protected $fillable = [
        'three_d_limit',
    ];
}