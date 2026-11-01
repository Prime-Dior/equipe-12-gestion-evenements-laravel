<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $query = Event::query()->with('category')->upcoming();
        $this->includeRegistrationCount($query);

        $events = $query
            ->when($filters['q'] ?? null, fn (Builder $builder, string $term) => $builder->where('title', 'like', "%{$term}%"))
            ->when($filters['category'] ?? null, fn (Builder $builder, int $categoryId) => $builder->where('category_id', $categoryId))
            ->when($filters['date'] ?? null, fn (Builder $builder, string $date) => $builder->whereDate('date', $date))
            ->orderBy('date')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        return view('events.index', compact('events', 'categories', 'filters'));
    }

    public function show(Event $event): View
    {
        $query = Event::query()->with('category');
        $this->includeRegistrationCount($query);
        $event = $query->findOrFail($event->id);

        return view('events.show', compact('event'));
    }

    public function adminIndex(): View
    {
        $query = Event::query()->with('category');
        $this->includeRegistrationCount($query);

        $events = $query->orderByDesc('date')->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.events.form', ['event' => new Event(), 'categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        Event::create($this->validatedEvent($request));

        return redirect()->route('admin.events.index')->with('success', 'L’événement a été créé.');
    }

    public function edit(Event $event): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.events.form', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validatedEvent($request);

        if (Schema::hasTable('registrations') && DB::table('registrations')->where('event_id', $event->id)->count() > $data['capacity']) {
            throw ValidationException::withMessages([
                'capacity' => 'La capacité ne peut pas être inférieure au nombre d’inscriptions existantes.',
            ]);
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'L’événement a été modifié.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if (Schema::hasTable('registrations') && DB::table('registrations')->where('event_id', $event->id)->exists()) {
            return back()->with('error', 'Cet événement possède des inscriptions. Il ne peut pas être supprimé.');
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'L’événement a été supprimé.');
    }

    private function validatedEvent(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'date' => ['required', 'date', 'after:now'],
            'location' => ['required', 'string', 'max:180'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);
    }

    private function includeRegistrationCount(Builder $query): void
    {
        if (Schema::hasTable('registrations')) {
            $query->withCount('registrations');
        }
    }
}
