<?php

namespace App\Console\Commands;

use App\Mail\TestEmail;
use App\Mail\WinnerNotificationMail;
use App\Models\Sweepstakes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:sendtest {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a test email to the specified address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        Mail::to($email)->send(new WinnerNotificationMail(Sweepstakes::first()));
        // Mail::to($email)->send(new TestEmail());

        $this->info('Test email sent to ' . $email);
    }
}
