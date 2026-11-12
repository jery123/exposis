<x-app-layout>
  <main class="project-submit-page community-submissions">
    <div class="project-submit-page__container">
      <a class="community-project-detail__back" href="{{ route('community.projects.index') }}">← Retour à mes projets</a>
      <header class="project-submit-page__hero">
        <span class="project-submit-page__eyebrow"><span aria-hidden="true"></span> {{ $project->pole }} · {{ $project->status }}</span>
        <h1>{{ $project->title }}</h1>
        <p>{{ $project->summary }}</p>
        @if ($project->published_at)
          <a class="community-project-detail__public-link" href="{{ route('public.projects.show', $project) }}">Voir la page publique du projet <span aria-hidden="true">↗</span></a>
        @else
          <p class="community-project-detail__notice" role="status">Ce projet reste privé pendant sa validation.</p>
        @endif
      </header>

      <article class="community-project-detail">
        <section aria-labelledby="project-description-heading">
          <h2 id="project-description-heading">Description du projet</h2>
          <div class="community-project-detail__body">{!! \Illuminate\Support\Str::markdown($project->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
        </section>

        @if ($project->team_members)
          <section aria-labelledby="project-team-heading">
            <h2 id="project-team-heading">L’équipe du projet</h2>
            <ul class="community-project-detail__team">
              @foreach ($project->team_members as $member)
                <li>{{ $member }}</li>
              @endforeach
            </ul>
          </section>
        @endif

        @if ($project->repo_url)
          <section aria-labelledby="project-link-heading">
            <h2 id="project-link-heading">Lien du projet</h2>
            <a href="{{ $project->repo_url }}" rel="noopener noreferrer" target="_blank">{{ $project->repo_url }} <span aria-hidden="true">↗</span></a>
          </section>
        @endif

        @if ($project->images->isNotEmpty())
          <section aria-labelledby="project-images-heading">
            <h2 id="project-images-heading">Images du projet</h2>
            <div class="community-project-detail__gallery">
              @foreach ($project->images as $index => $image)
                <a href="{{ route('community.projects.images.show', [$project, $image]) }}" target="_blank" rel="noopener noreferrer">
                  <img src="{{ route('community.projects.images.show', [$project, $image]) }}" alt="Image {{ $index + 1 }} du projet {{ $project->title }}">
                </a>
              @endforeach
            </div>
          </section>
        @endif

        <section aria-labelledby="project-report-heading">
          <h2 id="project-report-heading">Rapport du projet</h2>
          @if ($project->report)
            <p>{{ $project->report->original_name }} · {{ $project->report->approved_at ? 'Validé pour le public' : 'En attente de validation' }}</p>
          @else
            <p>Aucun rapport transmis pour le moment.</p>
          @endif
        </section>
      </article>
    </div>
  </main>
</x-app-layout>
