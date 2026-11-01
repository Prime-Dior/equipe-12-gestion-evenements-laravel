@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
<h2>Créer un compte</h2>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <label for="name">Nom</label>
    <input
        id="name"
        name="name"
        value="{{ old('name') }}"
        required
        maxlength="100">
    @error('name')
    <p class="error">{{ $message }}</p>
    @enderror

    <label for="email">Adresse e-mail</label>
    <input
        id="email"
        type="email"
        name="email"
        value="{{ old('email') }}"
        required>
    @error('email')
    <p class="error">{{ $message }}</p>
    @enderror

    <label for="password">Mot de passe (8 caractères minimum)</label>
    <input
        id="password"
        type="password"
        name="password"
        required>
    @error('password')
    <p class="error">{{ $message }}</p>
    @enderror

    <label for="password_confirmation">Confirmer le mot de passe</label>
    <input
        id="password_confirmation"
        type="password"
        name="password_confirmation"
        required>

    <button type="submit">S'inscrire</button>
</form>
<p>Déjà inscrit ? <a href="{{ route('login') }}">Connectez-vous</a>.</p>
@endsection