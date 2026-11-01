@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<h2>Se connecter</h2>

<form method="POST" action="{{ route('login') }}">
    @csrf

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

    <label for="password">Mot de passe</label>
    <input
        id="password"
        type="password"
        name="password"
        required>
    @error('password')
    <p class="error">{{ $message }}</p>
    @enderror

    <label>
        <input type="checkbox" name="remember" value="1" style="width: auto">
        Rester connecté
    </label>

    <button type="submit">Se connecter</button>
</form>

<p>
    Pas encore de compte ?
    <a href="{{ route('register') }}">Créez-en un</a>.
</p>
@endsection