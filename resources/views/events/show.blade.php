@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="detail-wrap">
    <a class="back-link" href="{{ route('events.index') }}"><span aria-hidden="true">←</span> Tous les événements</a>

    @if (session('success'))
        <p role="status">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p role="alert">{{ session('error') }}</p>
    @endif

    <div class="detail-grid">
        <article class="detail-main">
            <div class="event-art detail-art event-art-coral">
                <span class="art-orbit art-orbit-one"></span>
                <span class="art-orbit art-orbit-two"></span>
                <span class="art-symbol">✳</span>
                <span class="art-label">{{ $event->category->name }}</span>
            </div>
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
                <h2>Informations pratiques</h2>
                <p>Capacité : {{ $event->capacity }} places. Il reste actuellement {{ $event->places_remaining }} place(s).</p>
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
                <h2>Une place<br>vous attend.</h2>
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
