
@extends('layouts.app')

@section('title', 'Mes inscriptions')

@section('content')
<section class="content-section">
    <p class="eyebrow">MON ESPACE</p>
    <h1>Mes inscriptions</h1>

    @if (session('success'))
        <p role="status">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p role="alert">{{ session('error') }}</p>
    @endif

    @forelse ($registrations as $registration)
        <article class="event-card" style="margin-top: 16px; padding: 20px;">
            <h2>{{ $registration->event->title }}</h2>
            <p>{{ $registration->event->date->format('d/m/Y à H:i') }}</p>
            <p>{{ $registration->event->location }}</p>

            <form method="POST" action="{{ route('registrations.destroy', $registration) }}">
                @csrf
                @method('DELETE')
                <button class="button button-outline" type="submit">Annuler mon inscription</button>
            </form>
        </article>
    @empty
        <p>Vous n’êtes inscrit à aucun événement pour le moment.</p>
    @endforelse
</section>
@endsection
