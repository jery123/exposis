@extends('layouts.public')
@section('title', 'Projets | Recherche et prototypes ouverts | EXPOSIS')
@section('description', 'Les projets de recherche, de prototypage et de formation du laboratoire EXPOSIS, filtrables par domaine.')
@section('content')
<div class="ph"><div class="w"><h1>Projets du laboratoire.</h1><p class="lead">Filtrez par domaine de recherche.</p></div></div>
<section><div class="w">
  <p class="lead">Les membres partagent ici leurs recherches, prototypes et solutions, avec les personnes qui les ont construits.</p>
  <p class="field-note" style="margin-top:12px">
    @auth
      <a href="{{ route('community.projects.create') }}">Membre d’EXPOSIS ? Proposez votre projet.</a>
    @else
      <a href="{{ route('register') }}">Rejoindre la communauté</a> pour proposer un projet.
    @endauth
  </p>
  <div class="fl" role="group" aria-label="Filtrer par domaine">
    <a href="{{ route('public.projects.index') }}"><button type="button" aria-pressed="{{ $pole ? 'false' : 'true' }}">Tous</button></a>
    @foreach (\App\Models\PublicProject::POLES as $p)<a href="{{ route('public.projects.index', ['pole' => $p]) }}"><button type="button" aria-pressed="{{ $pole === $p ? 'true' : 'false' }}">{{ $p }}</button></a>@endforeach
  </div>
  @if ($projects->isEmpty())<p class="empty">Aucun projet publié dans ce domaine pour l’instant.</p>@endif
  <div class="grid">
    @foreach ($projects as $p)
      <article class="card"><div style="background:var(--o);height:88px;border-bottom:2px solid var(--ink)" aria-hidden="true"></div>
        <div class="bd"><span class="tags"><b>{{ $p->pole }}</b> · {{ $p->status }}@if($p->is_sample) · Exemple @endif</span><h3>{{ $p->title }}</h3><p>{{ $p->summary }}</p>
        @if($p->team_members)<p class="tags">{{ count($p->team_members) }} participant(s)</p>@endif
        <a class="btn g s" href="{{ route('public.projects.show', $p) }}">Lire le projet</a></div></article>
    @endforeach
    <article class="card cta-card"><div class="bd"><h3>Votre projet ici ?</h3><p>Étude, prototype ou recherche : décrivez votre idée.</p><a class="btn s" href="{{ route('public.submissions.create') }}">Soumettre un projet</a></div></article>
  </div>
</div></section>
@endsection
