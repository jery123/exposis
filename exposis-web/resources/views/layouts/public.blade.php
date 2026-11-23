<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EXPOSIS — Les idées se construisent ensemble')</title>

    <meta name="description" content="@yield('meta_description', 'Welcome to Exposis.')">

    <meta name="robots" content="@yield('meta_robots', 'index, follow')">

    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:description" content="@yield('meta_description', 'Welcome to Exposis.')">
    <meta property="og:site_name" content="Exposis">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Exposis')">
    <meta name="twitter:description" content="@yield('meta_description', 'Welcome to Exposis.')">

    <meta name="description" content="@yield('description', 'Communauté de recherche, d’expérimentation et d’innovation en informatique en Côte d’Ivoire.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'EXPOSIS')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
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
