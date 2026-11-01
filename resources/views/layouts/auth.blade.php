<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · Eventia</title>
    <link rel="stylesheet" href="{{ asset('css/eventia.css') }}">
</head>
<body class="auth-page">
    <a class="skip-link" href="#auth-form">Aller au formulaire</a>
    <main class="auth-shell">
        <aside class="auth-aside">
            <a class="brand auth-brand" href="{{ route('events.index') }}" aria-label="Eventia, accueil">
                <span class="brand-mark">e</span>eventia<span class="brand-period">.</span>
            </a>
            <div class="auth-aside-copy">
                <p class="eyebrow">LES RENCONTRES QUI NOUS RASSEMBLENT</p>
                <h1>Les belles idées commencent par une rencontre.</h1>
                <p>Découvrez des événements, partagez vos passions et réservez votre place.</p>
            </div>
            <a class="auth-back-link" href="{{ route('events.index') }}">← Retour aux événements</a>
        </aside>

        <section class="auth-content" id="auth-form">
            <div class="auth-form-wrap">
                @if (session('success'))
                    <p class="flash flash-success" role="status">{{ session('success') }}</p>
                @endif
                @if (session('error'))
                    <p class="flash flash-error" role="alert">{{ session('error') }}</p>
                @endif
                @if ($errors->any())
                    <div class="flash flash-error" role="alert">
                        <strong>Vérifiez les informations saisies.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
