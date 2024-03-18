<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sweepstakes;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessSweepstakesWinner;

class ProcessSweepstakes extends Command
{
    protected $signature = 'sweepstakes:process';
    protected $description = 'Process sweepstakes draws and send emails to winners';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $lockName = 'sweepstakes_process_lock';

        if (Cache::add($lockName, true, 120)) { // Lock for 2 minutes
            Sweepstakes::query()
                ->where('draw_time', '<=', now())
                ->where('is_winner_notified', false)
                ->chunk(100, function ($sweepstakes) {
                    foreach ($sweepstakes as $sweepstakesItem) {
                        ProcessSweepstakesWinner::dispatch($sweepstakesItem);

                        $sweepstakesItem->is_over = true;
                        $sweepstakesItem->save();
                    }
                });

            Cache::forget($lockName); // Release lock
            $this->info('Sweepstakes processing completed.');
        } else {
            $this->info('Another sweepstakes process is already running.');
        }
    }
}
