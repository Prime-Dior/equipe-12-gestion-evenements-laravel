
<article class="event-card">
    <div class="event-card-body">
        <p class="card-category">{{ $event->category->name }}</p>
        <h2><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a></h2>
        <div class="event-card-meta">
            <span>{{ $event->date->translatedFormat('d F Y · H:i') }}</span>
            <span>{{ $event->location }}</span>
        </div>
        <div class="card-footer">
            <span class="availability">{{ $event->places_remaining }} place(s) restante(s)</span>
            <a class="card-link" href="{{ route('events.show', $event) }}">Détails <span aria-hidden="true">→</span></a>
        </div>
    </div>
</article>
