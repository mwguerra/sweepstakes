<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipantStoreRequest;
use App\Models\Sweepstakes;

class SweepstakesParticipationController extends Controller
{
    public function store(ParticipantStoreRequest $request, Sweepstakes $sweepstakes)
    {
        // Carbon isPast compares with the current time in the same timezone of the item being compared to.
        // https://github.com/briannesbitt/Carbon/blob/4da5451e63e2f784f1a3d8fac1e8b6ab3e7f6424/src/Carbon/Traits/Comparison.php#L451
        if ($sweepstakes->is_over || $sweepstakes->draw_time_in_timezone->isPast()) {
            return redirect()->back()->withErrors('This sweepstakes has ended.');
        }

        $sweepstakes->participants()->create([
            'email' => $request->email,
        ]);

        return redirect()->back()->with('message', 'You have been successfully entered into the sweepstakes!');
    }
}
