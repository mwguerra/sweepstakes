<?php

use App\Models\Participant;
use App\Utils\SweepstakesEngine;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('sweepstakes:process')->everyMinute()->withoutOverlapping();
