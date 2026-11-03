@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="detail-wrap">
    <a class="back-link" href="{{ route('events.index') }}"><span aria-hidden="true">←</span> Tous les événements</a>

    <div class="detail-grid">
        <article class="detail-main">
            <p class="eyebrow detail-eyebrow">
                {{ $event->category->name }} <span>·</span> {{ $event->date->translatedFormat('l d F Y') }}
            </p>
            <h1>{{ $event->title }}</h1>
            <p class="detail-lead">{{ $event->description }}</p>
            <div class="detail-info">
                <div>
                    <span class="info-icon" aria-hidden="true">⌖</span>
                    <div>
                        <small>LE LIEU</small>
                        <strong>{{ $event->location }}</strong>
                    </div>
                </div>
                <div>
                    <span class="info-icon" aria-hidden="true">◷</span>
                    <div>
                        <small>DATE ET HEURE</small>
                        <strong>{{ $event->date->format('d/m/Y à H:i') }}</strong>
                    </div>
                </div>
            </div>
            <section class="about-event">
                <h2>À propos</h2>
                <p>Capacité de l’événement : {{ $event->capacity }} personnes.</p>
            </section>
        </article>

        <aside class="registration-panel">
            <p class="eyebrow">DISPONIBILITÉ</p>
            @if ($event->isPast())
                <h2>Événement passé</h2>
                <p class="places-note">Les inscriptions sont closes.</p>
            @elseif ($event->isFull())
                <h2>Événement complet</h2>
                <p class="places-note">Il ne reste plus de place disponible.</p>
            @elseif ($isRegistered)
                <h2>Vous êtes inscrit</h2>
                <p class="places-note">Votre place pour cet événement est réservée.</p>
            @else
                <h2>Participer</h2>
                <p class="places-note"><span class="availability-dot"></span>{{ $event->places_remaining }} place(s) restante(s)</p>
                @auth
                    <form action="{{ route('registrations.store', $event) }}" method="POST">
                        @csrf
                        <button class="button" type="submit">S’inscrire à cet événement</button>
                    </form>
                @else
                    <p class="form-footnote">Connectez-vous pour participer à cet événement.</p>
                    <a class="button" href="{{ route('login') }}">Se connecter</a>
                @endauth
            @endif
        </aside>
    </div>
</div>
@endsection
