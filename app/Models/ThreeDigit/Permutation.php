<?php

namespace App\Models\ThreeDigit;

use App\Jobs\CheckForThreeDWinnersWithPermutations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permutation extends Model
{
    use HasFactory;

    protected $fillable = ['digit'];

    protected static function booted()
    {
        static::created(function ($prize) {
            CheckForThreeDWinnersWithPermutations::dispatch($prize);
        });
    }
}
