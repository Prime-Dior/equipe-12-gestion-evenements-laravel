<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $registrations = $request->user()
            ->registrations()
            ->with('event')
            ->latest()
            ->get();

        return view('registrations.index', compact('registrations'));
    }

    public function store(Request $request, Event $event)
    {
        $result = DB::transaction(function () use ($request, $event): string {
            $event = Event::query()
                ->whereKey($event->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($event->registrations()
                ->where('user_id', $request->user()->getKey())
                ->exists()) {
                return 'already_registered';
            }

            if ($event->registrations()->count() >= $event->capacity) {
                return 'full';
            }

            $event->registrations()->create([
                'user_id' => $request->user()->getKey(),
            ]);

            return 'registered';
        });

        if ($result === 'already_registered') {
            return redirect()->route('registrations.index')
                ->with('error', 'Vous êtes déjà inscrit à cet événement.');
        }

        if ($result === 'full') {
            return redirect()->route('registrations.index')
                ->with('error', 'Cet événement affiche complet.');
        }

        return redirect()->route('registrations.index')
            ->with('success', 'Votre inscription a bien été enregistrée.');
    }

    public function destroy(Request $request, Registration $registration)
    {
        abort_unless(
            $request->user()->registrations()->whereKey($registration->getKey())->exists(),
            403
        );

        $registration->delete();

        return redirect()->route('registrations.index')
            ->with('success', 'Votre inscription a été annulée.');
    }
}
