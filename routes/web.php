<?php

use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SweepstakesController;
use App\Http\Controllers\SweepstakesParticipationController;
use App\Mail\TestEmail;
use App\Mail\WinnerNotificationMail;
use App\Models\Sweepstakes;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('sweepstakes', SweepstakesController::class)
        ->parameters([
            'sweepstakes' => 'sweepstakes',
        ])
        ->except(['show']);

    Route::get('mail/test', function () {
        $testEmails = [
            'mwguerra@gmail.com',
            'marcelo@likker.com.br'
        ];

        foreach ($testEmails as $email) {
            Mail::to($email)->send(new WinnerNotificationMail(Sweepstakes::first()));
        }

        return 'Test emails sent!';
    });

    Route::get('/sweepstakes/{sweepstakes}/files/download/{file:original_name}', [FileDownloadController::class, 'download'])->name('files.download');
});

require __DIR__.'/auth.php';

Route::get('/{sweepstakes:slug}', [SweepstakesController::class, 'show'])->name('sweepstakes.show');
Route::post('/{sweepstakes:slug}/participate', [SweepstakesParticipationController::class, 'store'])->name('sweepstakes.participate');
