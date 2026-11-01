@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
<div class="auth-heading">
    <p class="eyebrow">REJOIGNEZ EVENTIA</p>
    <h2>Créer un compte</h2>
    <p>Inscrivez-vous pour réserver votre place aux événements.</p>
</div>

<form class="auth-form" method="POST" action="{{ route('register.store') }}">
    @csrf

    <div class="auth-field">
        <label for="name">Nom</label>
        <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="100" autofocus>
        @error('name')
            <p class="auth-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="auth-field">
        <label for="email">Adresse e-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        @error('email')
            <p class="auth-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="auth-field">
        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" autocomplete="new-password" required>
        <small>8 caractères minimum</small>
        @error('password')
            <p class="auth-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="auth-field">
        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
    </div>

    <button class="button auth-submit" type="submit">Créer mon compte <span aria-hidden="true">→</span></button>
</form>

<p class="auth-switch">Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></p>
@endsection