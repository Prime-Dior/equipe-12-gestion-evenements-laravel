<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><meta name="description" content="Eventia, les événements et inscriptions de la communauté IFRI-UAC.">
    <title>@yield('title', 'Accueil') · Eventia</title><link rel="stylesheet" href="{{ asset('css/eventia.css') }}">
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="site-header"><a class="brand" href="{{ route('home') }}" aria-label="Eventia, accueil"><span class="brand-mark">e</span>eventia<span class="brand-period">.</span></a><nav class="main-nav" aria-label="Navigation principale"><a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Explorer</a><a href="{{ route('admin.events.index') }}" @if(request()->routeIs('admin.*')) aria-current="page" @endif>Organisateur</a><a href="{{ route('registrations.index') }}" @if(request()->routeIs('registrations.*')) aria-current="page" @endif>Inscriptions</a></nav><a class="header-cta" href="{{ route('home') }}#evenements">Trouver un événement <span aria-hidden="true">↗</span></a></header>
<main id="contenu">@if(session('success'))<div class="flash flash-success" role="status">{{ session('success') }}</div>@endif @if(session('error'))<div class="flash flash-error" role="alert">{{ session('error') }}</div>@endif @if($errors->any())<div class="flash flash-error" role="alert"><strong>Quelques informations sont à vérifier.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif @yield('content')</main>
<footer class="site-footer"><a class="brand footer-brand" href="{{ route('home') }}"><span class="brand-mark">e</span>eventia<span class="brand-period">.</span></a><p>Les bonnes idées méritent une salle pleine.</p><span>Projet d’initiation aux frameworks · IFRI-UAC · GL3 · Équipe 12</span></footer>
</body></html>
