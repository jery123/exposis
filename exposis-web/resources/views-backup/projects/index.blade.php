<x-app-layout>
  <main class="member-projects">
    <div class="member-projects__container">
      @if (session('status'))
        <p class="member-projects__alert" role="status">{{ session('status') }}</p>
      @endif

      <section class="member-projects__intro" aria-labelledby="projects-heading">
        <div class="member-projects__intro-copy">
          <span class="member-projects__eyebrow"><span aria-hidden="true"></span> Espace membre · EXPOSIS</span>
          <h1 id="projects-heading">Vos idées,<br><em>en mouvement.</em></h1>
          <p>Retrouvez vos demandes et suivez leur évolution avec l’équipe EXPOSIS.</p>
        </div>
        <div class="member-projects__actions">
          <a class="member-projects__button member-projects__button--primary" href="{{ route('projects.create') }}">
            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            Soumettre une demande
          </a>
          <a class="member-projects__button member-projects__button--secondary" href="{{ route('community.projects.create') }}">
            Partager un projet terminé
            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none"><path d="M5 15 15 5M6 5h9v9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
        </div>
      </section>

      <section class="member-projects__stats" aria-label="Résumé de vos projets">
        <article class="member-projects__stat">
          <span class="member-projects__stat-icon member-projects__stat-icon--orange" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 7.5h6l2 2h8v9H4v-11Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M4 7.5V6h6l2 2" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
          </span>
          <span class="member-projects__stat-label">Demandes envoyées</span>
          <strong>{{ $stats['total'] }}</strong>
          <span class="member-projects__stat-note">Depuis votre espace membre</span>
        </article>
        <article class="member-projects__stat">
          <span class="member-projects__stat-icon member-projects__stat-icon--sand" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.5"/><path d="M12 7.5v5l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
          <span class="member-projects__stat-label">En cours d’examen</span>
          <strong>{{ $stats['active'] }}</strong>
          <span class="member-projects__stat-note">Entre les mains de l’équipe</span>
        </article>
        <article class="member-projects__stat">
          <span class="member-projects__stat-icon member-projects__stat-icon--green" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none"><path d="m7 12.5 3.2 3.2L17.5 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.5"/></svg>
          </span>
          <span class="member-projects__stat-label">Réponses reçues</span>
          <strong>{{ $stats['resolved'] }}</strong>
          <span class="member-projects__stat-note">Évaluées par EXPOSIS</span>
        </article>
      </section>

      <section class="member-projects__list-section" aria-labelledby="project-list-heading">
        <div class="member-projects__list-heading">
          <div>
            <span class="member-projects__eyebrow">Votre espace de travail</span>
            <h2 id="project-list-heading">Toutes vos demandes</h2>
          </div>
          @if ($projects->total() > 0)
            <span class="member-projects__count">{{ $projects->total() }} projet{{ $projects->total() > 1 ? 's' : '' }}</span>
          @endif
        </div>

        @if ($projects->isEmpty())
          <div class="member-projects__empty">
            <span class="member-projects__empty-icon" aria-hidden="true">
              <svg viewBox="0 0 48 48" fill="none"><path d="M8 13h12l4 4h16v20H8V13Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 13V9h12l4 4" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M24 22v10m-5-5h10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </span>
            <span class="member-projects__eyebrow">Un premier pas suffit</span>
            <h3>Votre prochain projet commence ici.</h3>
            <p>Décrivez votre idée ou votre besoin. Vous pourrez ensuite suivre les échanges et l’évaluation depuis cet espace.</p>
            <a class="member-projects__button member-projects__button--primary" href="{{ route('projects.create') }}">
              Soumettre mon premier projet
              <svg aria-hidden="true" viewBox="0 0 20 20" fill="none"><path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          </div>
        @else
          <ul class="member-projects__grid">
            @foreach ($projects as $p)
              @php
                $statusClass = match ($p->status) {
                    'submitted' => 'received',
                    'in_review' => 'review',
                    'needs_info' => 'attention',
                    'evaluated', 'accepted' => 'complete',
                    'declined' => 'closed',
                    default => 'received',
                };
              @endphp
              <li>
                <a class="member-projects__card" href="{{ route('projects.show', $p) }}">
                  <span class="member-projects__card-top">
                    <span class="member-projects__reference">{{ $p->reference }}</span>
                    <span class="member-projects__status member-projects__status--{{ $statusClass }}">
                      <span aria-hidden="true"></span>{{ $p->statusLabel() }}
                    </span>
                  </span>
                  <span class="member-projects__card-title">{{ $p->title }}</span>
                  <span class="member-projects__type">{{ $p->typeLabel() }}</span>
                  <span class="member-projects__card-bottom">
                    <span>Soumis le {{ $p->created_at->format('d/m/Y') }}</span>
                    <span class="member-projects__view">Voir le suivi <span aria-hidden="true">↗</span></span>
                  </span>
                </a>
              </li>
            @endforeach
          </ul>
          @if ($projects->hasPages())
            <div class="member-projects__pagination">{{ $projects->links() }}</div>
          @endif
        @endif
      </section>

      <aside class="member-projects__community">
        <span class="member-projects__community-mark" aria-hidden="true">∞</span>
        <div>
          <span class="member-projects__eyebrow">Projet déjà réalisé ?</span>
          <h2>Faites connaître le travail de votre équipe.</h2>
          <p>Présentez un projet terminé, ajoutez des images et un lien. Après validation, il pourra rejoindre le catalogue de la communauté.</p>
        </div>
        <a href="{{ route('community.projects.create') }}">Présenter un projet <span aria-hidden="true">↗</span></a>
      </aside>
    </div>
  </main>
</x-app-layout>
