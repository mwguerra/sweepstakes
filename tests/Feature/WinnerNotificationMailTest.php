<?php

use Illuminate\Support\Facades\Mail;
use App\Mail\WinnerNotificationMail;
use App\Models\Sweepstakes;
use App\Models\File;

it('sends winner notification mail without attachments', function () {
    Mail::fake();

    $sweepstakes = Sweepstakes::factory()->create(); // Ensure your factory sets up a valid state for the sweepstakes

    // Assume you have a way to trigger the sending of the WinnerNotificationMail, such as through a job or directly in the test
    Mail::to('example@example.com')->send(new WinnerNotificationMail($sweepstakes));

    Mail::assertSent(WinnerNotificationMail::class, function ($mail) use ($sweepstakes) {
        // Check that the mail was sent without attachments
        return $mail->getSweepstakes()->is($sweepstakes) && count($mail->attachments()) === 0;
    });
});

it('sends winner notification mail with attachments', function () {
    Mail::fake();

    $sweepstakes = Sweepstakes::factory()
        ->has(File::factory()->count(3), 'winnerEmailFiles')
        ->create();

    Mail::to('example@example.com')->send(new WinnerNotificationMail($sweepstakes));

    Mail::assertSent(WinnerNotificationMail::class, function ($mail) use ($sweepstakes) {
        // Check that the mail was sent and contains attachments
        return $mail->getSweepstakes()->is($sweepstakes) && count($mail->attachments()) > 0;
    });
});
