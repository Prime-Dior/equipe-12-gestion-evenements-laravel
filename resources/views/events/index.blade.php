@extends('layouts.app')

@section('title', 'Événements')

@section('content')
<section class="content-section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">EVENTIA</p>
            <h2>Événements à venir</h2>
        </div>
        <p class="section-intro">{{ $events->count() }} événement(s)</p>
    </div>

    @if (session('success'))
        <p role="status">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p role="alert">{{ session('error') }}</p>
    @endif

    <div class="event-grid">
        @forelse ($events as $event)
            <article class="event-card">
                <div class="event-card-body">
                    <div class="event-card-date">
                        <span class="date-day">{{ $event->date->format('d') }}</span>
                        <span class="date-month">{{ $event->date->format('M') }}</span>
                    </div>
                    <div class="event-card-copy">
                        <p class="card-category">{{ $event->category->name }}</p>
                        <h3>{{ $event->title }}</h3>
                        <p class="card-location">{{ $event->location }}</p>
                    </div>
                    <div class="card-footer">
                        <span class="availability">
                            {{ max(0, $event->capacity - $event->registrations_count) }} place(s) restante(s)
                        </span>
                        <a class="button button-outline" href="{{ route('events.show', $event) }}">Voir</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <h3>Aucun événement pour le moment</h3>
                <p>Les événements publiés apparaîtront ici.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
