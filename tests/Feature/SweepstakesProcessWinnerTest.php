<?php

use App\Jobs\ProcessSweepstakesWinner;
use App\Mail\WinnerNotificationMail;
use App\Models\Participant;
use App\Models\Sweepstakes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

it('selects due sweepstakes for processing', function () {
    // Create a sweepstakes that should be processed
    $sweepstakesToProcess = Sweepstakes::factory()->create([
        'draw_time' => now()->subDay(),
        'is_winner_notified' => false,
    ]);

    // Create a sweepstakes that should not be processed
    Sweepstakes::factory()->create([
        'draw_time' => now()->addDay(),
        'is_winner_notified' => false,
    ]);

    // Mocking the Job dispatching
    Queue::fake();

    // Run the command
    $this->artisan('sweepstakes:process')->assertExitCode(0);

    // Assert the job was dispatched for the correct sweepstakes
    Queue::assertPushed(ProcessSweepstakesWinner::class, function ($job) use ($sweepstakesToProcess) {
        return $job->getSweepstakes()->id === $sweepstakesToProcess->id;
    });

    // Assert the job was not dispatched for the sweepstakes that should not be processed
    Queue::assertNotPushed(ProcessSweepstakesWinner::class, function ($job) use ($sweepstakesToProcess) {
        return $job->getSweepstakes()->id !== $sweepstakesToProcess->id;
    });
});

it('handles locks to prevent overlapping executions', function () {
    // Set a lock to simulate an ongoing process
    Cache::put('sweepstakes_process_lock', true, 120);

    // Attempt to run the command
    $this->artisan('sweepstakes:process')
        ->expectsOutput('Another sweepstakes process is already running.')
        ->assertExitCode(0);

    // Clear the lock
    Cache::forget('sweepstakes_process_lock');
});

it('updates sweepstakes and notifies winners upon processing', function () {
    Mail::fake();

    // Create a sweepstakes with participants
    $sweepstakes = Sweepstakes::factory()->has(Participant::factory()->count(5))->create([
        'draw_time' => now()->subDay(),
        'is_winner_notified' => false,
    ]);

    // Run the command
    $this->artisan('sweepstakes:process')->assertExitCode(0);

    // Assert the sweepstakes is marked as over
    expect($sweepstakes->fresh()->is_over)->toBeTrue();
    expect($sweepstakes->fresh()->is_winner_notified)->toBeTrue();

    // Assert a winner notification was sent
    Mail::assertSent(WinnerNotificationMail::class);
});
