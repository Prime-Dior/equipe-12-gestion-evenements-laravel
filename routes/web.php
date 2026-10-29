<?php

use App\Support\EventiaPreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ces routes ne servent qu'à prévisualiser les vues. Remplacez leur source temporaire par vos contrôleurs métier.
Route::get('/', function (Request $request) {
    $events = collect(EventiaPreview::events())
        ->when($request->query('q'), fn ($items, $q) => $items->filter(fn ($event) => str_contains(mb_strtolower($event['title']), mb_strtolower($q))))
        ->when($request->query('category'), fn ($items, $category) => $items->where('category', $category))
        ->when($request->query('date'), fn ($items, $date) => $items->filter(fn ($event) => $event['starts_at']->toDateString() === $date))
        ->values();

    $categories = collect(EventiaPreview::events())->pluck('category')->unique()->values();

    return view('welcome', compact('events', 'categories'));
})->name('home');

Route::get('/evenements/{event}', function (int $event) {
    $event = collect(EventiaPreview::events())->firstWhere('id', $event);
    abort_if(!$event, 404);

    return view('events.show', compact('event'));
})->name('events.show');

Route::post('/evenements/{event}/inscriptions', fn () => back()->with('info', 'Maquette uniquement : aucune inscription n’a été enregistrée.'))
    ->name('registrations.store');

Route::prefix('organisateur/evenements')->name('admin.events.')->group(function () {
    Route::get('/', fn () => view('admin.events.index', ['events' => EventiaPreview::events()]))->name('index');
    Route::get('/creation', fn () => view('admin.events.form'))->name('create');
    Route::post('/', fn () => back()->with('info', 'Maquette uniquement : aucun événement n’a été créé.'))->name('store');
    Route::put('/{event}', fn () => back()->with('info', 'Maquette uniquement : aucune modification n’a été enregistrée.'))->name('update');
    Route::delete('/{event}', fn () => back()->with('info', 'Maquette uniquement : aucun événement n’a été supprimé.'))->name('destroy');
    Route::get('/{event}/modification', function (int $event) {
        $event = collect(EventiaPreview::events())->firstWhere('id', $event);
        abort_if(!$event, 404);

        return view('admin.events.form', compact('event'));
    })->name('edit');
});

Route::get('/inscriptions', fn () => view('registrations.index', ['registrations' => EventiaPreview::registrations()]))
    ->name('registrations.index');

Route::patch('/inscriptions/{registration}', fn () => back()->with('info', 'Maquette uniquement : aucune inscription n’a été annulée.'))
    ->name('registrations.cancel');
