<?php

namespace App\Jobs;

use App\Models\Sweepstakes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WinnerNotificationMail; // Assume you have this Mailable

class ProcessSweepstakesWinner implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Sweepstakes $sweepstakes;

    public function __construct(Sweepstakes $sweepstakes)
    {
        $this->sweepstakes = $sweepstakes;
    }

    public function handle(): void
    {
        $winner = $this->sweepstakes->determineWinner();

        if (!$winner) {
            return;
        }

        Mail::to($winner->email)->send(new WinnerNotificationMail($this->sweepstakes));

        $this->sweepstakes->winner_notified = true;
        $this->sweepstakes->save();
    }
}
