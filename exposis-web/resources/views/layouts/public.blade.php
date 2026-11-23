<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.seo')
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/exposis.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    @if (request()->routeIs('about'))
        <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    @endif
    @stack('styles')
</head>
<body>
    <a class="skip-link" href="#contenu">Aller au contenu</a>
    @include('layouts.site-header')
    <main id="contenu">
        @yield('content')
    </main>
    @include('layouts.site-footer')
    <script src="{{ asset('js/exposis.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
