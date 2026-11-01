@extends('layouts.app')

@section('title', $event->title)

@section('content')
<section class="detail-wrap">
    <a class="back-link" href="{{ route('events.index') }}"><span>←</span> Tous les événements</a>

    @if (session('success'))
        <p role="status">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p role="alert">{{ session('error') }}</p>
    @endif

    <div class="detail-grid">
        <div class="detail-main">
            <p class="eyebrow detail-eyebrow">
                {{ $event->category->name }} <span>·</span> {{ $event->date->format('d/m/Y à H:i') }}
            </p>
            <h1>{{ $event->title }}</h1>
            <p class="detail-lead">{{ $event->description }}</p>
            <div class="detail-info">
                <div>
                    <span class="info-icon" aria-hidden="true">⌖</span>
                    <span>
                        <small>LIEU</small>
                        <strong>{{ $event->location }}</strong>
                    </span>
                </div>
                <div>
                    <span class="info-icon" aria-hidden="true">○</span>
                    <span>
                        <small>PLACES</small>
                        <strong>{{ max(0, $event->capacity - $registrationsCount) }} / {{ $event->capacity }} disponibles</strong>
                    </span>
                </div>
            </div>
        </div>

        <aside class="registration-panel">
            <p class="eyebrow">PARTICIPATION</p>
            <h2>Rejoignez-nous</h2>

            @auth
                @if ($isRegistered)
                    <p>Vous êtes déjà inscrit à cet événement.</p>
                @elseif ($registrationsCount >= $event->capacity)
                    <p>Désolé, cet événement est complet.</p>
                @else
                    <form action="{{ route('registrations.store', $event) }}" method="POST">
                        @csrf
                        <button class="button" type="submit">S’inscrire à cet événement</button>
                    </form>
                @endif
            @else
                <p>Connectez-vous pour participer à cet événement.</p>
                <a class="button" href="{{ route('login') }}">Se connecter</a>
            @endauth
        </aside>
    </div>
</section>
@endsection
