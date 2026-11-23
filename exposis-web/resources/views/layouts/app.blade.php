<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace membre | EXPOSIS')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <meta name="description" content="@yield('meta_description', 'Welcome to Exposis.')">

    <meta name="robots" content="@yield('meta_robots', 'index, follow')">

    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:title" content="@yield('title', 'Exposis')">
    <meta property="og:description" content="@yield('meta_description', 'Welcome to Exposis.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Exposis">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Exposis')">
    <meta name="twitter:description" content="@yield('meta_description', 'Welcome to Exposis.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/exposis.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body>
    <a class="skip-link" href="#contenu">Aller au contenu</a>
    @include('layouts.site-header')
    @isset($header)
        <div class="portal"><div class="portal__inner">{{ $header }}</div></div>
    @endisset
    <div id="contenu">
        {{ $slot }}
    </div>
    @include('layouts.site-footer')
    <script src="{{ asset('js/exposis.js') }}" defer></script>
</body>
</html>
