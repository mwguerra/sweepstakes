<?php

namespace App\Http\Controllers;

use App\Http\Requests\SweepstakesStoreRequest;
use App\Http\Requests\SweepstakesUpdateRequest;
use App\Models\User;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Models\Sweepstakes;
use App\Models\Participant;
use App\Mail\WinnerNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SweepstakesController extends Controller
{
    public function index()
    {
        $sweepstakes = Sweepstakes::with('winner:id,email')->withCount('participants')->get();

        return Inertia::render('Sweepstakes/Index', compact('sweepstakes'));
    }

    public function show(Sweepstakes $sweepstakes)
    {
        $totalParticipants = $sweepstakes->participants()->count();
        $sweepstakes->load('winner');

        return Inertia::render('Sweepstakes/Show', compact('sweepstakes', 'totalParticipants'));
    }

    public function edit(Sweepstakes $sweepstakes)
    {
        $timezones = DateTimeZone::listIdentifiers();
        $sweepstakes->load('winnerEmailFiles');

        return Inertia::render('Sweepstakes/Edit', compact('sweepstakes', 'timezones'));
    }

    public function create()
    {
        $timezones = DateTimeZone::listIdentifiers();

        return Inertia::render('Sweepstakes/Edit', compact('timezones'));
    }

    public function store(SweepstakesStoreRequest $request)
    {
        $sweepstakes = Sweepstakes::create($request->all());

        if ($sweepstakes && $request->hasFile('newFiles')) {
            foreach ($request->file('newFiles') as $file) {
                $path = $file->store('public/prizes');
                $sweepstakes->winnerEmailFiles()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()->route('sweepstakes.index');
    }

    public function update(SweepstakesUpdateRequest $request, Sweepstakes $sweepstakes)
    {
        $sweepstakes->update($request->all());

        // Handle deleted files
        $keptFileIds = $request->input('keptFiles', []);
        $sweepstakes->winnerEmailFiles()->whereNotIn('id', $keptFileIds)->delete();

        // Handle new files
        if ($request->hasFile('newFiles')) {
            foreach ($request->file('newFiles') as $file) {
                $path = $file->store('public/prizes');
                $sweepstakes->winnerEmailFiles()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()->route('sweepstakes.index');
    }

    public function destroy(Sweepstakes $sweepstakes) {
        $sweepstakes->delete();

        return redirect()->route('sweepstakes.index');
    }
}
