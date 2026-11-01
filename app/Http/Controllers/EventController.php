<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->with('category')
            ->withCount('registrations')
            ->orderBy('date')
            ->get();

        return view('events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        $event->load('category');
        $registrationsCount = $event->registrations()->count();
        $isRegistered = auth()->check()
            && $event->registrations()->where('user_id', auth()->id())->exists();

        return view('events.show', compact('event', 'registrationsCount', 'isRegistered'));
    }
}
