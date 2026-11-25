@props(['title'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }} · Administration EXPOSIS</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell">
  <a class="admin-skip-link" href="#admin-content">Aller au contenu</a>
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <a class="admin-brand" href="{{ route('admin.dashboard') }}">
        <x-application-logo class="admin-brand__logo" />
        <span><strong>EXPOSIS</strong><small>Administration</small></span>
      </a>
      <p class="admin-sidebar__label">Espace de travail</p>
      <nav class="admin-nav" aria-label="Administration">
        @if (Auth::user()->hasPermissionTo('dashboard.view'))
          <a @class(['admin-nav__link', 'is-active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}"><span aria-hidden="true">⌂</span> Vue d’ensemble</a>
        @endif
        @if (Auth::user()->hasPermissionTo('projects.manage'))
          <a @class(['admin-nav__link', 'is-active' => request()->routeIs('admin.projects.*')]) href="{{ route('admin.projects.index') }}"><span aria-hidden="true">▤</span> Demandes reçues</a>
          <a class="admin-nav__sublink" href="{{ route('admin.projects.export') }}">Exporter les demandes ↗</a>
        @endif
        @if (Auth::user()->hasPermissionTo('community_projects.manage'))
          <a @class(['admin-nav__link', 'is-active' => request()->routeIs('admin.public-projects.*')]) href="{{ route('admin.public-projects.index') }}"><span aria-hidden="true">∞</span> Projets communautaires</a>
        @endif
      </nav>
      @if (Auth::user()->hasPermissionTo('users.manage') || Auth::user()->hasPermissionTo('roles.manage'))
        <p class="admin-sidebar__label">Accès et équipe</p>
        <nav class="admin-nav" aria-label="Gestion des accès">
          @if (Auth::user()->hasPermissionTo('users.manage'))
            <a @class(['admin-nav__link', 'is-active' => request()->routeIs('admin.users.*')]) href="{{ route('admin.users.index') }}"><span aria-hidden="true">♙</span> Comptes membres</a>
          @endif
          @if (Auth::user()->hasPermissionTo('roles.manage'))
            <a @class(['admin-nav__link', 'is-active' => request()->routeIs('admin.roles.*')]) href="{{ route('admin.roles.index') }}"><span aria-hidden="true">⚙</span> Rôles et permissions</a>
          @endif
        </nav>
      @endif
      <div class="admin-sidebar__bottom">
        <a class="admin-nav__link" href="{{ route('home') }}"><span aria-hidden="true">↗</span> Voir le site</a>
        <a class="admin-nav__link" href="{{ route('profile.edit') }}"><span aria-hidden="true">◉</span> Mon profil</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="admin-nav__link" type="submit"><span aria-hidden="true">↪</span> Déconnexion</button></form>
      </div>
    </aside>
    <div class="admin-main">
      <header class="admin-topbar">
        <div><span class="admin-topbar__eyebrow">EXPOSIS · ESPACE SÉCURISÉ</span><h1>{{ $title }}</h1></div>
        <div class="admin-topbar__user"><span class="admin-topbar__avatar" aria-hidden="true">{{ mb_substr(Auth::user()->name, 0, 1) }}</span><span><strong>{{ Auth::user()->name }}</strong><small>{{ Auth::user()->roleLabel() }}</small></span></div>
      </header>
      <main class="admin-content" id="admin-content">{{ $slot }}</main>
      <footer class="admin-footer"><span>EXPOSIS · Administration</span><span>Recherche · Expérimentation · Innovation</span></footer>
    </div>
  </div>
</body>
</html>
