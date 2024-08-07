<?php

namespace App\Console\Commands;

use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMatchStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update match status to closed when match_start_date and res_date are over';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentDate = Carbon::now()->toDateString();
        $currentTime = Carbon::now()->setTimezone('Asia/Yangon')->format('H:i');
        //Log::info('Current Time:' . $currentTime);

        if ($currentTime == '23:55') {
            LotteryThreeDigitPivot::where('match_status', 'open')
                ->whereDate('res_date', $currentDate)
                ->update(['match_status' => 'closed']);

            $this->info('Match statuses updated successfully.');
        } else {
            $this->info('Current time is not 23:55. No status updates performed.');
        }

        return 0;
    }
}
