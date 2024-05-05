<?php

namespace Database\Seeders;

use App\Models\ThreeDigit\ResultDate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ThreeDResultDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startYear = 2024; // Starting year
        $endYear = 2044;   // 20 years from 2024 to 2044

        // Loop through each year
        for ($year = $startYear; $year <= $endYear; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                // Default seeding for the start of each month
                if ($month != 5) { // Skip May 1st
                    $resultDate1 = Carbon::createFromDate($year, $month, 1);
                    $resultDate2 = Carbon::createFromDate($year, $month, 2);

                    ResultDate::create([
                        'result_date' => $resultDate1->format('Y-m-d'),
                        'result_time' => '15:30:00',
                        'match_start_date' => $resultDate2->format('Y-m-d'),
                        'status' => 'closed', // Default status
                        'endpoint' => 'https://77sportsmm.com',
                    ]);
                }
                // Special cases for May
                if ($month == 5) {
                    // First game in May: result on the 2nd, match start on the 3rd
                    $resultDateMay2 = Carbon::createFromDate($year, 5, 2);
                    $matchStartDateMay3 = Carbon::createFromDate($year, 5, 3);

                    ResultDate::create([
                        'result_date' => $resultDateMay2->format('Y-m-d'),
                        'match_start_date' => $matchStartDateMay3->format('Y-m-d'),
                        'result_time' => '15:30:00',
                        'status' => 'closed',
                        'endpoint' => 'https://77sportsmm.com',
                    ]);

                }

                $resultDate16 = Carbon::createFromDate($year, $month, 16);
                $resultDate17 = Carbon::createFromDate($year, $month, 17);

                ResultDate::create([
                    'result_date' => $resultDate16->format('Y-m-d'),
                    'match_start_date' => $resultDate17->format('Y-m-d'),
                    'result_time' => '15:30:00',
                    'status' => 'closed',
                    'endpoint' => 'https://77sportsmm.com/login',
                ]);

                // Special case for December 31st
                if ($month == 12) {
                    $resultDate31 = Carbon::createFromDate($year, 12, 31);
                    $matchStartDate17 = Carbon::createFromDate($year, 12, 17);

                    ResultDate::create([
                        'result_date' => $resultDate31->format('Y-m-d'),
                        'match_start_date' => $matchStartDate17->format('Y-m-d'),
                        'result_time' => '15:30:00',
                        'status' => 'closed',
                        'endpoint' => 'https://77sportsmm.com/login',
                    ]);
                }
            }
        }
    }
}
