<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'bank' => 'Bank A', // Required field
                'image' => '1.png',
                'phone' => '123-456-7890', // Required field
                'name' => 'John Doe', // Required field
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'bank' => 'Bank B',
                'image' => '2.png',
                'phone' => '987-654-3210',
                'name' => 'Jane Smith',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'bank' => 'Bank C',
                'image' => '3.png',
                'phone' => '987-654-3210',
                'name' => 'Jane Smith',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'bank' => 'Bank D',
                'image' => '4.png',
                'phone' => '987-654-3210',
                'name' => 'Jane Smith',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('banks')->insert($banks);
    }
}
