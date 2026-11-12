<x-app-layout>
  <main class="project-submit-page community-submissions">
    <div class="project-submit-page__container">
      @if (session('status'))
        <p class="community-submissions__status" role="status">{{ session('status') }}</p>
      @endif
      <header class="project-submit-page__hero">
        <span class="project-submit-page__eyebrow"><span aria-hidden="true"></span> Espace membre · Communauté</span>
        <h1>Mes projets<br><em>à partager.</em></h1>
        <p>Suivez vos propositions et transmettez leur rapport. Chaque projet et chaque rapport est examiné avant d’être rendu accessible aux visiteurs.</p>
        <a class="community-submissions__new" href="{{ route('community.projects.create') }}">Proposer un projet <span aria-hidden="true">↗</span></a>
      </header>

      <div class="community-submissions__list">
        @forelse ($projects as $project)
          <article class="community-submission-card">
            <div class="community-submission-card__summary">
              <div>
                <span class="community-submission-card__eyebrow">{{ $project->pole }} · {{ $project->status }} · {{ $project->images_count }} image(s)</span>
                <h2>{{ $project->title }}</h2>
                @if ($project->report)
                  <p class="community-submission-card__report">
                    Rapport : {{ $project->report->original_name }} ·
                    {{ $project->report->approved_at ? 'validé pour le public' : 'en attente de validation' }}
                  </p>
                @else
                  <p class="community-submission-card__report">Aucun rapport transmis pour le moment.</p>
                @endif
              </div>
              <a class="community-submission-card__badge {{ $project->published_at ? 'community-submission-card__badge--published' : '' }}" href="{{ route('community.projects.show', $project) }}">
                {{ $project->published_at ? 'Publié · Voir mon projet' : 'En cours de validation · Voir mon projet' }}
              </a>
            </div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('community.projects.report.store', $project) }}" class="community-report-form">
              @csrf
              <div class="project-submit-form__field">
                <label for="report-{{ $project->id }}">{{ $project->report ? 'Remplacer le rapport' : 'Ajouter le rapport' }}</label>
                <input id="report-{{ $project->id }}" type="file" name="report" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                <span class="project-submit-form__hint">PDF ou DOCX · 20 Mo maximum. Tout nouveau fichier doit être revalidé avant sa mise en ligne.</span>
                @foreach ($errors->get('report') as $message)
                  <span class="text-red-600" role="alert">{{ $message }}</span>
                @endforeach
              </div>
              <button class="community-report-form__submit" type="submit">Envoyer le rapport <span aria-hidden="true">↗</span></button>
            </form>
          </article>
        @empty
          <div class="community-submissions__empty">
            <span aria-hidden="true">∞</span>
            <h2>Votre première contribution commence ici.</h2>
            <p>Présentez un projet terminé ou en cours et partagez sa documentation avec la communauté.</p>
            <a class="community-submissions__new" href="{{ route('community.projects.create') }}">Proposer un projet <span aria-hidden="true">↗</span></a>
          </div>
        @endforelse
      </div>
      <div class="community-submissions__pagination">{{ $projects->links() }}</div>
    </div>
  </main>
</x-app-layout>
