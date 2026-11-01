
<article class="event-card">
    <a class="event-art event-art-coral" href="{{ route('events.show', $event) }}" tabindex="-1" aria-hidden="true">
        <span class="art-orbit art-orbit-one"></span><span class="art-orbit art-orbit-two"></span><span class="art-symbol" aria-hidden="true">✳</span><span class="art-label">{{ $event->category->name }}</span>
    </a>
    <div class="event-card-body">
        <div class="event-card-date"><span class="date-day">{{ $event->date->format('d') }}</span><span class="date-month">{{ $event->date->translatedFormat('M') }}</span><span class="date-time">{{ $event->date->format('H:i') }}</span></div>
        <div class="event-card-copy"><p class="card-category">{{ $event->category->name }}</p><h3><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a></h3><p class="card-location"><span aria-hidden="true">⌖</span> {{ $event->location }}</p></div>
        <div class="card-footer"><span class="availability"><span class="availability-dot"></span>{{ $event->places_remaining }} place(s) restante(s)</span><a class="arrow-link" href="{{ route('events.show', $event) }}" aria-label="Voir {{ $event->title }}">↗</a></div>
    </div>
</article>
