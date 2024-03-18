<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipantStoreRequest;
use App\Models\Sweepstakes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SweepstakesParticipationController extends Controller
{
    public function store(ParticipantStoreRequest $request, Sweepstakes $sweepstakes)
    {
        if ($sweepstakes->is_over || $sweepstakes->draw_time->isPast()) {
            return redirect()->back()->withErrors('This sweepstakes has ended.');
        }

        $sweepstakes->participants()->create([
            'email' => $request->email,
        ]);

        return redirect()->back()->with('message', 'You have been successfully entered into the sweepstakes!');
    }
}
