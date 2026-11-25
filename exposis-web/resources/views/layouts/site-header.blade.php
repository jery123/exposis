<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="EXPOSIS, accueil">
            <img src="{{ asset('images/exposis-logo-orange.png') }}" alt="EXPOSIS" width="132" height="79">
        </a>
        <nav aria-label="Navigation principale" class="main-nav">
            <a href="{{ route('about') }}">À propos</a>
            <a href="{{ route('home') }}#approche">Notre approche</a>
            <a href="{{ route('public.projects.index') }}">Projets</a>
            <a href="{{ route('home') }}#communaute">La communauté</a>
            {{-- <a href="{{ route('contact') }}">Con   tact</a> --}}
        </nav>
        <div class="mobile-navigation">
            <button aria-controls="mobile-menu" aria-expanded="false" aria-label="Ouvrir le menu" class="mobile-menu-toggle" id="mobile-menu-toggle" type="button">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <nav aria-label="Menu mobile" class="mobile-nav" id="mobile-menu" hidden>
                <a href="{{ route('about') }}">À propos</a>
                <a href="{{ route('home') }}#approche">Notre approche</a>
                <a href="{{ route('public.projects.index') }}">Projets</a>
                <a href="{{ route('home') }}#communaute">La communauté</a>
                {{-- <a href="{{ route('contact') }}">Contact</a> --}}
                @guest<a href="{{ route('login') }}">Connexion</a>@endguest
            </nav>
        </div>
        <div class="header-actions">
            @auth
                <div class="account-menu">
                    <button aria-controls="account-menu-options" aria-expanded="false" aria-label="Menu du compte {{ Auth::user()->name }}" class="account-trigger" id="account-menu-toggle" type="button">
                        <span aria-hidden="true" class="account-avatar">{{ collect(preg_split('/\s+/', trim(Auth::user()->name)))->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('') }}</span>
                        <svg aria-hidden="true" class="account-chevron" viewBox="0 0 16 16"><path d="m4 6 4 4 4-4"></path></svg>
                    </button>
                    <nav aria-label="Menu du compte" class="account-dropdown" id="account-menu-options" hidden>
                        <span class="account-name">{{ Auth::user()->name }}</span>
                        <a href="{{ route('profile.edit') }}">Mon profil</a>
                        <a href="{{ route('dashboard') }}">Mon tableau de bord</a>
                        <span class="account-name"></span>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Déconnexion</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </nav>      
                </div>
            @else
                <div class="account-actions"><a class="text-link sign-in" href="{{ route('login') }}">Connexion</a></div>
            @endauth
            <a class="button button-dark button-small" href="{{ route('public.submissions.create') }}">Soumettre une idée <span aria-hidden="true">↗</span></a>
        </div>
    </div>
</header>
