<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'EXPOSIS | Laboratoire d’informatique en Côte d’Ivoire')</title>
<meta name="description" content="@yield('description', 'EXPOSIS, laboratoire informatique ivoirien à but non lucratif.')">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:title" content="@yield('title', 'EXPOSIS')"><meta property="og:description" content="@yield('description', 'EXPOSIS')"><meta property="og:type" content="website"><meta property="og:url" content="{{ url()->current() }}">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%23E8610A'/%3E%3Cpath d='M32 32c4-9 14-9 14 0s-10 9-14 0-14-9-14 0 10 9 14 0z' fill='none' stroke='%2314110F' stroke-width='4' stroke-linecap='round'/%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,800&family=Hanken+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/exposis.css') }}">
</head>
<body>
<a class="sr" href="#main">Aller au contenu</a>
<header><div class="w">
  <a class="brand" href="{{ route('home') }}" aria-label="EXPOSIS, accueil"><img src="{{ asset('images/exposis-logo-orange.jpeg') }}" alt="EXPOSIS"><span class="sr">Accueil</span></a>
  <button class="burger" id="bg" aria-label="Menu" aria-expanded="false" aria-controls="nv">☰</button>
  <nav id="nv" aria-label="Navigation principale">
    <a @if(request()->routeIs('home')) aria-current="page" @endif href="{{ route('home') }}">Accueil</a>
    <a @if(request()->routeIs('about')) aria-current="page" @endif href="{{ route('about') }}">À propos</a>
    <a @if(request()->routeIs('services')) aria-current="page" @endif href="{{ route('services') }}">Domaines</a>
    <a @if(request()->routeIs('public.projects.*')) aria-current="page" @endif href="{{ route('public.projects.index') }}">Projets</a>
    <a @if(request()->routeIs('contact')) aria-current="page" @endif href="{{ route('contact') }}">Contact</a>
    @auth
      <a href="{{ route('community.projects.index') }}">Projets membres</a>
      <a href="{{ route('projects.index') }}">Mes demandes</a>
    @else
      <a href="{{ route('login') }}">Connexion</a>
      <a href="{{ route('register') }}">Rejoindre</a>
    @endauth
  </nav>
  <a class="btn s" style="margin-left:0" href="{{ route('public.submissions.create') }}">Soumettre un projet</a>
</div></header>
<main id="main">
@yield('content')
</main>
<footer class="dk"><div class="w"><div class="fg">
  <div><a class="brand" href="{{ route('home') }}"><img src="{{ asset('images/exposis-logo-white.jpeg') }}" alt="EXPOSIS"></a><p style="color:#bdb6ad;font-size:.95rem">Laboratoire de Recherche, d’Expérimentation et d’Innovation en Informatique.</p></div>
  <div><h3>Liens</h3><a href="{{ route('home') }}">Accueil</a><a href="{{ route('about') }}">À propos</a><a href="{{ route('services') }}">Services</a><a href="{{ route('public.projects.index') }}">Projets</a><a href="{{ route('contact') }}">Contact</a></div>
  <div><h3>Contact</h3><a href="mailto:contact-lab@exposis.net">contact-lab@exposis.net</a><a href="{{ route('public.submissions.create') }}">Soumettre un projet</a></div></div>
  <p class="cp">© {{ date('Y') }} EXPOSIS. Organisation à but non lucratif. <a href="{{ route('legal') }}" style="display:inline">Mentions légales</a>.</p></div></footer>
<script src="{{ asset('js/exposis.js') }}" defer></script>
</body>
</html>
