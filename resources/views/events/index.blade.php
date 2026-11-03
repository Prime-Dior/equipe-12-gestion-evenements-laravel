@extends('layouts.app')

@section('title', 'Événements à venir')

@section('content')
<section class="content-section" id="evenements">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Eventia</p>
            <h1>Événements à venir</h1>
        </div>
        <p class="section-intro">Ateliers, conférences et rencontres près de chez vous.</p>
    </div>

    <form class="filter-bar" method="GET" action="{{ route('events.index') }}">
        <label class="search-field">
            <span class="sr-only">Rechercher par titre</span>
            <span aria-hidden="true">⌕</span>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Quel événement recherchez-vous ?">
        </label>
        <label>
            <span class="sr-only">Filtrer par catégorie</span>
            <select name="category">
                <option value="">Toutes les catégories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="sr-only">Filtrer par date</span>
            <input type="date" name="date" value="{{ $filters['date'] ?? '' }}">
        </label>
        <button class="button" type="submit">Rechercher</button>
    </form>

    @if ($events->isEmpty())
        <div class="empty-state">
            <span class="empty-mark">✳</span>
            <h3>Aucun événement trouvé</h3>
            <p>Essayez d’autres filtres ou revenez bientôt pour les prochains rendez-vous.</p>
        </div>
    @else
        <div class="event-grid">
            @foreach ($events as $event)
                @include('components.event-card', ['event' => $event])
            @endforeach
        </div>
        <div class="pagination">{{ $events->links() }}</div>
    @endif
</section>
@endsection
