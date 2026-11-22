@extends('layouts.public')
@section('title', 'Projets de la communauté | EXPOSIS')
@section('description', 'Découvrez les projets de recherche et d’innovation portés par les membres d’EXPOSIS.')
@section('content')
    <section class="page-hero projects-hero">
        <div class="section-kicker"><span>EX / 03</span> Recherche en action</div>
        <h1>Des pistes explorées.<br><em>Des solutions partagées.</em></h1>
        <p>Chaque projet est une occasion d’apprendre ensemble et de faire émerger des réponses adaptées à nos réalités.</p>
        <div class="projects-hero-meta"><span><i aria-hidden="true"></i> Le laboratoire en mouvement</span><span>Des idées concrètes, portées par la communauté</span></div>
    </section>
    <section class="catalog-section section-wrap">
        <form action="{{ route('public.projects.index') }}" class="project-search" role="search">
            @if ($pole)<input name="pole" type="hidden" value="{{ $pole }}">@endif
            <label for="project-query">Explorer le catalogue</label>
            <div class="search-control">
                <input autocomplete="off" id="project-query" maxlength="100" name="q" placeholder="Ex. intelligence artificielle, Awa…" type="search" value="{{ $search }}">
                <button type="submit"><span aria-hidden="true">⌕</span> Rechercher</button>
            </div>
            @if ($search)
                <a class="search-reset" href="{{ route('public.projects.index', $pole ? ['pole' => $pole] : []) }}">Effacer la recherche</a>
            @endif
        </form>

        <div class="catalog-toolbar">
            <nav aria-label="Filtrer les projets par domaine" class="filter-list">
                <a @class(['filter-active' => ! $pole]) aria-current="{{ ! $pole ? 'page' : 'false' }}" href="{{ route('public.projects.index', $search ? ['q' => $search] : []) }}">Tous les domaines</a>
                @foreach (\App\Models\PublicProject::POLES as $area)
                    <a @class(['filter-active' => $pole === $area]) aria-current="{{ $pole === $area ? 'page' : 'false' }}" href="{{ route('public.projects.index', array_filter(['pole' => $area, 'q' => $search])) }}">{{ $area }}</a>
                @endforeach
            </nav>
            <a class="underlined-link" href="{{ route('community.projects.create') }}">Vous êtes membre ? Présentez un projet <span aria-hidden="true">↗</span></a>
        </div>

        @if ($projects->isNotEmpty())
            <div class="catalog-results-heading">
                <div><span class="section-kicker"><span>La sélection</span> Des projets publiés</span><h2>{{ $search ? 'Résultats de votre recherche' : 'Les projets de la communauté' }}</h2></div>
                <span class="catalog-results-count">{{ $projects->total() }} projet{{ $projects->total() > 1 ? 's' : '' }}</span>
            </div>
            <div class="catalog-grid">
                @foreach ($projects as $index => $project)
                    <article class="catalog-card catalog-card-{{ ($index % 3) + 1 }}">
                        @if ($project->images->isNotEmpty())
                            <a class="catalog-card-image" href="{{ route('public.projects.show', $project) }}" aria-label="Voir le projet {{ $project->title }}">
                                <img src="{{ url("/api/public/projects/{$project->slug}/images/{$project->images->first()->id}") }}" alt="{{ $project->title }}" loading="lazy">
                                @if ($project->images->count() > 1)<span class="catalog-image-count">+{{ $project->images->count() - 1 }} image{{ $project->images->count() > 2 ? 's' : '' }}</span>@endif
                            </a>
                        @else
                            <a class="catalog-card-art" href="{{ route('public.projects.show', $project) }}" aria-label="Voir le projet {{ $project->title }}"><span>EX / {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><span aria-hidden="true">∞</span><i aria-hidden="true"></i></a>
                        @endif
                        <div class="catalog-card-top">
                            <span class="catalog-pole">{{ $project->pole }}</span>
                            <span class="catalog-card-badges">
                                @if ($project->report?->approved_at && $project->report->approved_at->isPast())<span class="catalog-report-badge">Rapport disponible</span>@endif
                                <span class="catalog-status"><i aria-hidden="true"></i>{{ $project->status }}</span>
                            </span>
                        </div>
                        <div class="catalog-card-copy">
                            <h2><a href="{{ route('public.projects.show', $project) }}">{{ $project->title }}</a></h2>
                            <p>{{ $project->summary }}</p>
                            @if ($project->team_members)
                                <div class="project-card-team"><span class="team-label">Équipe projet</span><ul>@foreach (array_slice($project->team_members, 0, 3) as $member)<li>{{ $member }}</li>@endforeach</ul></div>
                            @endif
                        </div>
                        <div class="catalog-card-bottom">
                            <span>{{ count($project->team_members ?? []) ? count($project->team_members).' membre(s) dans l’équipe' : 'Projet EXPOSIS' }}</span>
                            <a href="{{ route('public.projects.show', $project) }}">Découvrir <span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                @endforeach
                <a class="catalog-invite" href="{{ route('public.submissions.create') }}">
                    <span class="invite-mark" aria-hidden="true">+</span>
                    <span class="section-kicker">Et si on explorait votre idée ?</span>
                    <strong>Le prochain projet inspirant pourrait être le vôtre.</strong>
                    <span class="underlined-link">Soumettre votre idée <span aria-hidden="true">↗</span></span>
                </a>
            </div>
            @if ($projects->hasPages())
                <nav class="catalog-pagination" aria-label="Pagination des projets">
                    {{ $projects->links() }}
                </nav>
            @endif
        @else
            <div class="catalog-message">
                <span class="empty-mark" aria-hidden="true">∞</span>
                <h2>{{ $search ? 'Aucun résultat pour cette recherche.' : 'Les projets se préparent.' }}</h2>
                <p>{{ $search ? 'Aucun projet ne correspond à cette recherche. Essayez d’autres mots-clés ou retirez le filtre.' : 'Les propositions publiées par les membres apparaîtront ici après validation par l’équipe.' }}</p>
                @if ($search)<a class="underlined-link" href="{{ route('public.projects.index', $pole ? ['pole' => $pole] : []) }}">Effacer la recherche ↗</a>@endif
                <a class="button button-dark button-small" href="{{ route('register') }}">Rejoindre la communauté <span aria-hidden="true">↗</span></a>
            </div>
        @endif
    </section>
@endsection
