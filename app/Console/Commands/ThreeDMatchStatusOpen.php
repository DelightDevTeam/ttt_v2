<?php

namespace App\Console\Commands;

use App\Models\ThreeD\ThreedSetting;
use App\Models\ThreeDigit\ResultDate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ThreeDMatchStatusOpen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:three-d-match-status-open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today('Asia/Yangon');
        ResultDate::where('match_start_date', $today)
            ->update(['status' => 'open']);

        $this->info('ThreedSettings status updated successfully.');
    }
}
