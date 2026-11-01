@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="auth-heading">
    <p class="eyebrow">Votre compte</p>
    <h2>Connexion</h2>
    <p>Connectez-vous pour gérer vos inscriptions.</p>
</div>

<form class="auth-form" method="POST" action="{{ route('login.store') }}">
    @csrf

    <div class="auth-field">
        <label for="email">Adresse e-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
        @error('email')
            <p class="auth-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="auth-field">
        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" autocomplete="current-password" required>
        @error('password')
            <p class="auth-field-error">{{ $message }}</p>
        @enderror
    </div>

    <label class="auth-remember">
        <input type="checkbox" name="remember" value="1">
        <span>Rester connecté</span>
    </label>

    <button class="button auth-submit" type="submit">Se connecter <span aria-hidden="true">→</span></button>
</form>

<p class="auth-switch">Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a></p>
@endsection
