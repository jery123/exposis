@extends('layouts.public')
@section('title', 'EXPOSIS — Les idées se construisent ensemble')
@section('description', 'Communauté de recherche, d’expérimentation et d’innovation en informatique en Côte d’Ivoire.')
@section('content')
    <section class="hero">
        <div class="hero-grid">
            <div class="hero-copy">
                <p class="eyebrow"><span class="status-dot"></span> Laboratoire indépendant · Côte d’Ivoire</p>
                <h1>Les idées ne changent rien. <em>Ce qu’on en fait, si.</em></h1>
                <p class="hero-intro">EXPOSIS est une communauté de jeunes diplômés qui apprend, expérimente et construit des projets innovants pour répondre aux enjeux d’ici.</p>
                <div class="hero-actions">
                    <a class="button button-orange" href="{{ route('public.submissions.create') }}">Soumettre un projet <span aria-hidden="true">↗</span></a>
                    <a class="button button-outline" href="{{ route('public.projects.index') }}">Découvrir les projets <span aria-hidden="true">↓</span></a>
                </div>
                <div class="hero-caption"><span class="caption-line"></span><span>La recherche prend vie quand elle se partage.</span></div>
            </div>
            <div class="hero-art" role="img" aria-label="Les étapes d’un projet EXPOSIS">
                <div class="art-orbit orbit-one"></div>
                <div class="art-orbit orbit-two"></div>
                <div class="art-center"><span class="art-mark">E.</span><span>une idée<br>en mouvement</span></div>
                <div class="orbit-note note-one"><span>01</span> Imaginer</div>
                <div class="orbit-note note-two"><span>02</span> Explorer</div>
                <div class="orbit-note note-three"><span>03</span> Prototyper</div>
                <div class="orbit-note note-four"><span>04</span> Partager</div>
            </div>
        </div>
        <div class="hero-meta">
            <span>01 / 03 <i></i></span>
            <span>Faire émerger le potentiel local.</span>
            <span>Abidjan · Afrique de l’Ouest</span>
        </div>
    </section>

    <section class="manifesto section-wrap" id="approche">
        <div class="section-kicker"><span>01</span> Notre raison d’être</div>
        <div class="manifesto-main">
            <h2>Un terrain de jeu pour les esprits <em>qui veulent bâtir.</em></h2>
            <div class="manifesto-note">
                <p>Nous croyons qu’une communauté forte se construit en partageant les savoirs et en donnant aux idées l’espace d’être testées.</p>
                <a class="underlined-link" href="#communaute">Ce qui nous rassemble <span aria-hidden="true">↗</span></a>
            </div>
        </div>
        <div class="principle-grid">
            <article class="principle-card"><span class="principle-number">01</span><h3>Apprendre en faisant</h3><p>Des projets concrets pour transformer la curiosité en compétences et en expérience.</p><span class="principle-arrow" aria-hidden="true">↗</span></article>
            <article class="principle-card"><span class="principle-number">02</span><h3>Chercher ensemble</h3><p>Des jeunes diplômés, chercheurs et partenaires qui croisent leurs savoirs.</p><span class="principle-arrow" aria-hidden="true">↗</span></article>
            <article class="principle-card"><span class="principle-number">03</span><h3>Construire utile</h3><p>Des solutions pensées pour les réalités de la Côte d’Ivoire et du continent.</p><span class="principle-arrow" aria-hidden="true">↗</span></article>
        </div>
    </section>

    <section class="focus-section">
        <div class="focus-inner">
            <div>
                <div class="section-kicker section-kicker-light"><span>02</span> Terrains d’exploration</div>
                <h2>La technologie comme outil.<br><em>Le progrès comme boussole.</em></h2>
            </div>
            <p>De la cybersécurité à la formation, nous expérimentons là où le numérique peut ouvrir de nouvelles possibilités.</p>
            <div class="focus-tags"><span>Cybersécurité</span><span>Intelligence artificielle</span><span>Logiciel libre</span><span>Réseaux &amp; cloud</span><span>Formation</span></div>
            <div class="focus-aside"><span>EX / 01</span><span>Explorer sans frontières.</span></div>
        </div>
    </section>

    <section class="projects-section section-wrap">
        <div class="section-heading">
            <div><div class="section-kicker"><span>03</span> Le travail en mouvement</div><h2>Des idées qui <em>prennent forme.</em></h2></div>
            <a class="underlined-link" href="{{ route('public.projects.index') }}">Tous les projets <span aria-hidden="true">↗</span></a>
        </div>
        @if ($featuredProjects->isNotEmpty())
            <div class="project-preview-grid">
                @foreach ($featuredProjects as $index => $project)
                    <a class="project-preview project-preview-{{ $index + 1 }}" href="{{ route('public.projects.show', $project) }}">
                        <span class="preview-index">0{{ $index + 1 }} / {{ $project->pole }}</span>
                        <span class="preview-title">{{ $project->title }}</span>
                        <span class="preview-summary">{{ $project->summary }}</span>
                        <span class="preview-arrow" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-projects">
                <div class="empty-mark" aria-hidden="true">∞</div>
                <div><span class="eyebrow">Le premier chapitre reste à écrire</span><h3>Les projets de la communauté arrivent ici.</h3><p>Rejoignez EXPOSIS et partagez ce que vous êtes en train de construire.</p></div>
                <a class="button button-dark button-small" href="{{ route('register') }}">Rejoindre la communauté <span aria-hidden="true">↗</span></a>
            </div>
        @endif
    </section>

    <section class="community-section" id="communaute">
        <div class="community-inner">
            <div><div class="section-kicker"><span>04</span> À plusieurs, autrement</div><h2>On ne construit pas<br>l’avenir <em>en solo.</em></h2></div>
            <div class="community-copy">
                <p>Membres, chercheurs, institutions ou porteurs d’idées : chacun peut contribuer à faire avancer un projet.</p>
                <div class="community-actions">
                    <a class="button button-orange" href="{{ route('register') }}">Rejoindre EXPOSIS <span aria-hidden="true">↗</span></a>
                    <a class="button button-light-outline" href="{{ route('public.submissions.create') }}">Proposer une idée <span aria-hidden="true">↗</span></a>
                </div>
                <a class="member-link" href="{{ route('community.projects.create') }}">Déjà membre ? Présentez votre projet et ses participants <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>

    <section class="closing-cta"><p>La prochaine idée utile peut commencer ici.</p><a href="{{ route('public.submissions.create') }}">Racontez-nous la vôtre <span aria-hidden="true">↗</span></a></section>
@endsection
