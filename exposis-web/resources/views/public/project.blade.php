@extends('layouts.public')
@section('title', $project->title . ' | EXPOSIS')
@section('description', \Illuminate\Support\Str::limit($project->summary, 155))
@section('og_type', 'article')
@if ($project->images->isNotEmpty())
    @section('og_image', rtrim(config('seo.site_url'), '/').'/api/public/projects/'.rawurlencode($project->slug).'/images/'.$project->images->first()->id)
    @section('og_image_alt', 'Illustration du projet '.$project->title)
@endif
@section('content')
    <article class="project-detail section-wrap">
        <a class="back-link" href="{{ route('public.projects.index') }}"><span aria-hidden="true">←</span> Retour au catalogue</a>
        <header class="project-detail-header">
            <div class="project-detail-tags"><span class="catalog-pole">{{ $project->pole }}</span><span class="catalog-status"><i aria-hidden="true"></i>{{ $project->status }}</span></div>
            <h1>{{ $project->title }}</h1>
            <p class="project-lead">{{ $project->summary }}</p>
            @if ($project->team_members)<span class="project-detail-team-count">{{ count($project->team_members) }} membre{{ count($project->team_members) > 1 ? 's' : '' }} dans l’équipe</span>@endif
        </header>
        <div class="project-detail-grid">
            <div class="project-detail-main">
                @if ($project->images->isNotEmpty())
                    <section class="project-gallery" aria-label="Images du projet {{ $project->title }}">
                        @foreach ($project->images as $index => $image)
                            <a class="project-gallery-image" href="{{ url("/api/public/projects/{$project->slug}/images/{$image->id}") }}" target="_blank" rel="noreferrer" aria-label="Ouvrir l’image {{ $index + 1 }} du projet {{ $project->title }}">
                                <img src="{{ url("/api/public/projects/{$project->slug}/images/{$image->id}") }}" alt="{{ $project->title }} — image {{ $index + 1 }}" loading="lazy">
                            </a>
                        @endforeach
                    </section>
                @endif
                @if ($project->body)
                    <section class="project-body-section"><span class="section-kicker"><span>01</span> À propos</span><h2>Une idée, une démarche, un impact.</h2><div class="project-body">{!! \App\Support\ProjectBody::render($project->body) !!}</div></section>
                @endif
                @if ($project->team_members)
                    <section class="team-section"><span class="section-kicker"><span>02</span> La communauté</span><h2>Celles et ceux qui le font avancer</h2><ul>@foreach ($project->team_members as $member)<li>{{ $member }}</li>@endforeach</ul></section>
                @endif
            </div>
            <aside class="project-detail-aside" aria-label="Informations sur le projet">
                <span class="project-aside-kicker">Fiche projet</span>
                <div class="project-aside-row"><span>Domaine</span><strong>{{ $project->pole }}</strong></div>
                <div class="project-aside-row"><span>État</span><strong class="project-aside-status"><i aria-hidden="true"></i>{{ $project->status }}</strong></div>
                <div class="project-aside-row"><span>Équipe</span><strong>{{ count($project->team_members ?? []) ? count($project->team_members).' membre(s)' : 'Communauté EXPOSIS' }}</strong></div>
                @if ($project->report?->approved_at && $project->report->approved_at->isPast())
                    <a class="button button-orange project-external-link" href="{{ route('public.projects.report.preview', $project) }}">@auth Consulter le rapport complet @else Lire l’aperçu du rapport @endauth <span aria-hidden="true">↗</span></a>
                @endif
                @if ($project->repo_url)
                    <a class="button {{ $project->report?->approved_at ? 'button-outline' : 'button-orange' }} project-external-link" href="{{ $project->repo_url }}" target="_blank" rel="noreferrer">Découvrir le projet <span aria-hidden="true">↗</span></a>
                @else
                    @if(!       Auth::user())
                        <a class="button {{ $project->report?->approved_at ? 'button-outline' : 'button-dark' }} project-external-link" href="{{ route('register') }}">Rejoindre la communauté<span aria-hidden="true">↗</span></a>
                    @endif
                @endif
            </aside>
        </div>
    </article>
@endsection
