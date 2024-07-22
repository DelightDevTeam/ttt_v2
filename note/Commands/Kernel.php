<?php

namespace App\Console;

use App\Jobs\CheckForEarlyEveningWinners;
use App\Jobs\CheckForEarlyMonringWinners;
use App\Jobs\CheckForEveningWinners;
use App\Jobs\CheckForMorningWinners;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        Commands\MorningSessionOpen::class,
        Commands\MorningSessionStatus::class,
        Commands\EveningSessionOpen::class,
        Commands\EveningSessionStatus::class,
        Commands\MorningPrizeStatusOpen::class,
        Commands\EveningPrizeStatusOpen::class,
        Commands\MorningPrizeStatusClose::class,
        Commands\EveningPrizeStatusClose::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        //$schedule->job(new CheckForEarlyMonringWinners)->dailyAt('9:30');
        //$schedule->job(new CheckForMorningWinners)->dailyAt('12:00');
        //$schedule->job(new CheckForEarlyEveningWinners)->dailyAt('2:30');
        //$schedule->job(new CheckForEveningWinners)->dailyAt('16:30');
        $schedule->command('session:morning-status-open')->oneDay();
        $schedule->command('session:morning-prize-status-open')->oneDay();
        $schedule->command('session:morning-prize-status-close')->oneDay();
        $schedule->command('session:evening-status-open')->oneDay();
        $schedule->command('session:morning-status')->oneDay(); // session close
        $schedule->command('session:evening-status')->oneDay(); // session close
        $schedule->command('session:eveing-prize-status-open')->oneDay();
        $schedule->command('session:evening-prize-status-close')->oneDay();
    }

    // protected function schedule(Schedule $schedule): void
    // {
    //     // $schedule->command('inspire')->hourly();
    // //     $schedule->call(function () {
    // //     DB::table('lottery_two_digit_pivot')
    // //         ->join('lotteries', 'lotteries.id', '=', 'lottery_two_digit_pivot.lottery_id')
    // //         ->where('lotteries.session', 'morning')
    // //         ->delete();
    // // })->dailyAt('12:00');
    //  $schedule->job(new CheckForMorningWinners)->dailyAt('12:00');
    // $schedule->job(new CheckForEveningWinners)->dailyAt('16:30');
    // }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
