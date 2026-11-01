
@extends('layouts.app')
@section('title', $event->exists ? 'Modifier un événement' : 'Créer un événement')
@section('content')
<section class="admin-form-page">
    <a class="back-link" href="{{ route('admin.events.index') }}"><span aria-hidden="true">←</span> Administration</a>
    <div class="form-heading"><p class="eyebrow">Administration</p><h1>{{ $event->exists ? 'Modifier l’événement' : 'Créer un événement' }}</h1><p>Renseignez les informations pratiques.</p></div>
    <form class="event-form" method="POST" action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}">
        @csrf
        @if($event->exists) @method('PUT') @endif
        <div class="form-row">
            <label>Titre<input type="text" name="title" value="{{ old('title', $event->title) }}" maxlength="160" required>@error('title')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Catégorie<select name="category_id" required><option value="">Choisir une catégorie</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $event->category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id')<small class="field-error">{{ $message }}</small>@enderror</label>
        </div>
        <label>Description<textarea name="description" rows="5" maxlength="5000" required>{{ old('description', $event->description) }}</textarea>@error('description')<small class="field-error">{{ $message }}</small>@enderror</label>
        <div class="form-row">
            <label>Date et heure<input type="datetime-local" name="date" value="{{ old('date', $event->date?->format('Y-m-d\TH:i')) }}" required>@error('date')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Lieu<input type="text" name="location" value="{{ old('location', $event->location) }}" maxlength="180" required>@error('location')<small class="field-error">{{ $message }}</small>@enderror</label>
        </div>
        <label>Capacité (nombre de places)<input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1" max="100000" required>@error('capacity')<small class="field-error">{{ $message }}</small>@enderror</label>
        <div class="form-actions"><a class="button button-muted" href="{{ route('admin.events.index') }}">Annuler</a><button class="button" type="submit">Enregistrer <span aria-hidden="true">→</span></button></div>
    </form>
</section>
@endsection
