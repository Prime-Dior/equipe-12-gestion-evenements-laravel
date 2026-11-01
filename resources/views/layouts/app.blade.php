<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Eventia')</title>
    <link rel="stylesheet" href="{{ asset('css/eventia.css') }}">
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
</html>
