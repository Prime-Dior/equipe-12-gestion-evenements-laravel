@extends('layouts.app')
@section('title', 'Événements à venir')
@section('content')
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">LES RENCONTRES QUI NOUS RASSEMBLENT</p>
        <h1>Le prochain bon moment<br>commence ici.</h1>
        <p>Ateliers, conférences et rendez-vous culturels au Bénin. Trouvez une rencontre qui vous ressemble.</p>
        <a class="button button-light" href="#evenements">Découvrir les événements <span aria-hidden="true">↓</span></a>
    </div>
    <div class="hero-note"><span class="hero-note-mark">E.</span><span>Les idées prennent vie<br>quand on se rencontre.</span></div>
</section>
<section class="content-section" id="evenements">
    <div class="section-heading"><div><p class="eyebrow">LE CALENDRIER</p><h2>À ne pas manquer</h2></div><p class="section-intro">Des expériences à partager, près de chez vous.</p></div>
    <form class="filter-bar" method="GET" action="{{ route('events.index') }}">
        <label class="search-field"><span class="sr-only">Rechercher par titre</span><span aria-hidden="true">⌕</span><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Quel événement recherchez-vous ?"></label>
        <label><span class="sr-only">Filtrer par catégorie</span><select name="category"><option value="">Toutes les catégories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label><span class="sr-only">Filtrer par date</span><input type="date" name="date" value="{{ $filters['date'] ?? '' }}"></label>
        <button class="button" type="submit">Rechercher</button>
    </form>
    @if($events->isEmpty())
        <div class="empty-state"><span class="empty-mark">✳</span><h3>Aucun événement trouvé</h3><p>Modifiez vos filtres ou revenez bientôt pour découvrir les prochains rendez-vous.</p></div>
    @else
        <div class="event-grid">@foreach($events as $event)@include('components.event-card', ['event' => $event])@endforeach</div>
        <div class="pagination">{{ $events->links() }}</div>
    @endif
</section>
@endsection
