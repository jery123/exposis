@extends('layouts.public')
@section('title', $project->title . ' | EXPOSIS')
@section('description', \Illuminate\Support\Str::limit($project->summary, 155))
@section('content')
<section><div class="w" style="max-width:760px">
  <a href="{{ route('public.projects.index') }}" style="font-weight:700">← Retour aux projets</a>
  <p class="tags" style="margin-top:28px"><b>{{ $project->pole }}</b> · {{ $project->status }} · <time datetime="{{ $project->published_at->toDateString() }}">{{ $project->published_at->translatedFormat('j F Y') }}</time>@if($project->is_sample) · Exemple @endif</p>
  <h1 style="font-size:clamp(2.2rem,6vw,4rem);margin:12px 0 28px">{{ $project->title }}</h1>
  @if ($project->team_members)
    <section class="team" aria-labelledby="team-heading">
      <h2 id="team-heading">L’équipe du projet</h2>
      <ul>@foreach ($project->team_members as $member)<li>{{ $member }}</li>@endforeach</ul>
    </section>
  @endif
  <div class="prose">{!! \Illuminate\Support\Str::markdown($project->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
  @if ($project->repo_url)<p style="margin-top:32px"><a class="btn g" href="{{ $project->repo_url }}" rel="noopener" target="_blank">Voir le code</a></p>@endif
</div></section>
<style>.prose h2{font-size:1.8rem;margin:28px 0 10px}.prose p{color:var(--mute);margin:0 0 16px}.prose a{color:#B84A00}.team{margin:0 0 28px;padding:20px;border:2px solid var(--ink);border-radius:16px;background:var(--mist)}.team h2{font-size:1.3rem}.team ul{display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:14px 0 0}.team li{padding:5px 12px;border:1px solid var(--line);border-radius:99px;background:#fff;font-weight:700}</style>
@endsection
