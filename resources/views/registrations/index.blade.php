
@extends('layouts.app')

@section('title', 'Mes inscriptions')

@section('content')
<section class="content-section">
    <p class="eyebrow">Mon espace</p>
    <div class="section-heading">
        <div><h2>Mes inscriptions</h2></div>
        <p class="section-intro">Les événements auxquels vous participez.</p>
    </div>

    @if ($registrations->isEmpty())
        <div class="registration-empty">
            <p>Vous n’avez pas encore d’inscription.</p>
            <a class="button" href="{{ route('events.index') }}">Parcourir les événements</a>
        </div>
    @else
        <div class="registration-list">
            @foreach ($registrations as $registration)
                <article class="registration-card">
                    <div>
                        <h2><a href="{{ route('events.show', $registration->event) }}">{{ $registration->event->title }}</a></h2>
                        <p>{{ $registration->event->date->translatedFormat('d F Y · H:i') }}</p>
                        <p>{{ $registration->event->location }}</p>
                    </div>
                    <form method="POST" action="{{ route('registrations.destroy', $registration) }}">
                        @csrf
                        @method('DELETE')
                        <button class="button button-outline" type="submit">Annuler</button>
                    </form>
                </article>
            @endforeach
        </div>
    @endif
</section>
@endsection
