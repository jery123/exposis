<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-brand">
            <a class="brand" href="{{ route('home') }}" aria-label="EXPOSIS, accueil">
                <img src="{{ asset('images/exposis-logo-white.png') }}" alt="EXPOSIS" width="118" height="67">
            </a>
            <p>Un laboratoire vivant pour apprendre, explorer et bâtir des solutions utiles.</p>
        </div>
        <div>
            <p class="footer-label">Explorer</p>
            <a href="{{ route('about') }}">À propos d’EXPOSIS</a>
            <a href="{{ route('services') }}">Domaines et services</a>
            <a href="{{ route('public.projects.index') }}">Projets de la communauté</a>
            <a href="{{ route('home') }}#approche">Notre approche</a>
        </div>
        <div>
            <p class="footer-label">Participer</p>
            <a href="{{ route('community.projects.create') }}">Proposer un projet membre</a>
            <a href="{{ route('public.submissions.create') }}">Soumettre une idée</a>
            <a href="{{ route('contact') }}">Nous contacter</a>
            <a href="{{ route('legal') }}">Mentions légales</a>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© {{ date('Y') }} EXPOSIS · Côte d’Ivoire</span>
        <span>Recherche · Expérimentation · Innovation</span>
    </div>
</footer>
