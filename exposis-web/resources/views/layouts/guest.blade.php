<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace membre | EXPOSIS')</title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
    <a class="auth-skip" href="#auth-main">Aller au formulaire</a>
    <header class="auth-header">
        <a class="auth-brand" href="/" aria-label="EXPOSIS, accueil">
            <img src="/images/exposis-logo-orange.jpeg" alt="EXPOSIS">
        </a>
        <a class="auth-back" href="/">Retour au site <span aria-hidden="true">↗</span></a>
    </header>
    <main class="auth-main" id="auth-main">
        <aside class="auth-aside">
            <span class="auth-kicker"><i></i> Recherche · Expérimentation · Innovation</span>
            <h1>Les idées avancent<br />mieux <em>ensemble.</em></h1>
            <p>Un espace pour les membres qui apprennent, partagent et construisent des projets utiles avec EXPOSIS.</p>
            <div class="auth-aside-note"><span>EX / CI</span><span>Communauté de recherche · Côte d’Ivoire</span></div>
        </aside>
        <section class="auth-panel" aria-label="Espace membre">
            <a class="auth-panel-logo" href="/" aria-label="Accueil EXPOSIS">
                <img src="/images/exposis-logo-orange.jpeg" alt="EXPOSIS">
            </a>
            {{ $slot }}
        </section>
    </main>
    <footer class="auth-footer">
        <span>© {{ date('Y') }} EXPOSIS · Côte d’Ivoire</span>
        <span>Votre espace pour bâtir ensemble.</span>
    </footer>
</body>
</html>
