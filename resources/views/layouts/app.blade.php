
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Événements') · Eventia</title>
    <link rel="stylesheet" href="{{ asset('css/eventia.css') }}">
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="site-header">
    <a class="brand" href="{{ route('events.index') }}" aria-label="Eventia, accueil"><span class="brand-mark">e</span>eventia<span class="brand-period">.</span></a>
    <nav class="main-nav" aria-label="Navigation principale">
        <a href="{{ route('events.index') }}" @if(request()->routeIs('events.*')) aria-current="page" @endif>Événements</a>
        @guest
            <a href="{{ route('login') }}">Connexion</a>
            <a href="{{ route('register') }}">Créer un compte</a>
        @endguest
        @auth
            <a href="{{ route('registrations.index') }}">Mes inscriptions</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.events.index') }}">Administration</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Déconnexion</button>
            </form>
        @endauth
    </nav>
</header>
<main id="contenu">
    @if(session('success'))<div class="flash flash-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="flash flash-error" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="flash flash-error" role="alert"><strong>Vérifiez les informations saisies.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
<footer class="site-footer">
    <a class="brand footer-brand" href="{{ route('events.index') }}"><span class="brand-mark">e</span>eventia<span class="brand-period">.</span></a>
    <p>Les bonnes idées méritent une salle pleine.</p>
    <span>Projet d’initiation aux frameworks · IFRI-UAC · GL3 · Équipe 12</span>
</footer>
</body>
</html>
