<x-app-layout>
  @php
    $steps = [
        ['title' => 'Demande reçue', 'description' => 'Votre projet a bien été transmis à EXPOSIS.'],
        ['title' => 'Étude en cours', 'description' => 'Notre équipe examine les besoins et la faisabilité.'],
        ['title' => 'Retour de l’équipe', 'description' => 'Retrouvez ici la décision et les échanges.'],
    ];
    $activeStep = match ($project->status) {
        'submitted' => 0,
        'in_review', 'needs_info' => 1,
        default => 2,
    };
    $statusTone = match ($project->status) {
        'accepted', 'evaluated' => 'success',
        'declined' => 'muted',
        'needs_info' => 'attention',
        default => 'progress',
    };
  @endphp

  <main class="project-tracking">
    <div class="project-tracking__container">
      <a class="project-tracking__back" href="{{ route('projects.index') }}">
        <span aria-hidden="true">←</span> Retour à mes demandes
      </a>

      @if (session('status'))
        <p class="project-tracking__alert" role="status">{{ session('status') }}</p>
      @endif

      @if ($errors->has('body'))
        <p class="project-tracking__alert project-tracking__alert--error" role="alert">{{ $errors->first('body') }}</p>
      @endif

      <header class="project-tracking__hero">
        <div class="project-tracking__hero-copy">
          <div class="project-tracking__eyebrow"><span aria-hidden="true"></span> Espace membre · Suivi de projet</div>
          <div class="project-tracking__status project-tracking__status--{{ $statusTone }}">
            <span aria-hidden="true"></span>{{ $project->statusLabel() }}
          </div>
          <h1>{{ $project->title }}</h1>
          <p>{{ $project->typeLabel() }}</p>
        </div>
        <div class="project-tracking__hero-mark" aria-hidden="true">
          <span>EX</span>
          <i></i><i></i><i></i>
        </div>
        <div class="project-tracking__hero-footer">
          <span>Référence <strong>{{ $project->reference }}</strong></span>
          <span>Déposé le {{ $project->created_at->translatedFormat('j F Y') }}</span>
        </div>
      </header>

      <section class="project-tracking__progress" aria-labelledby="project-progress-title">
        <div class="project-tracking__section-heading">
          <div>
            <span class="project-tracking__eyebrow">Votre parcours</span>
            <h2 id="project-progress-title">Chaque idée avance à son rythme.</h2>
          </div>
          <span class="project-tracking__step-count">Étape {{ $activeStep + 1 }} <span>/ 3</span></span>
        </div>
        <ol class="project-tracking__steps">
          @foreach ($steps as $index => $step)
            <li @class([
                'project-tracking__step',
                'is-complete' => $index < $activeStep || in_array($project->status, ['accepted', 'declined'], true),
                'is-current' => $index === $activeStep && ! in_array($project->status, ['accepted', 'declined'], true),
            ])>
              <span class="project-tracking__step-marker" aria-hidden="true">
                @if ($index < $activeStep || in_array($project->status, ['accepted', 'declined'], true))✓@else{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}@endif
              </span>
              <span class="project-tracking__step-copy">
                <strong>{{ $step['title'] }}</strong>
                <span>{{ $step['description'] }}</span>
              </span>
            </li>
          @endforeach
        </ol>
        @if ($project->status === 'needs_info')
          <p class="project-tracking__next-step" role="status"><span aria-hidden="true">!</span> L’équipe a besoin d’informations complémentaires. Consultez les échanges ci-dessous pour poursuivre le suivi.</p>
        @endif
      </section>

      <div class="project-tracking__layout">
        <div class="project-tracking__main">
          <section class="project-tracking__card project-tracking__brief" aria-labelledby="project-brief-title">
            <div class="project-tracking__card-heading">
              <span class="project-tracking__card-icon" aria-hidden="true">01</span>
              <div><span class="project-tracking__eyebrow">Le point de départ</span><h2 id="project-brief-title">Votre projet, en bref</h2></div>
            </div>
            <div class="project-tracking__brief-section">
              <h3>Le problème à résoudre</h3>
              <p>{{ $project->problem }}</p>
            </div>
            <div class="project-tracking__brief-section">
              <h3>Votre idée</h3>
              <p>{{ $project->description }}</p>
            </div>
            @if ($project->timeline || $project->budget)
              <div class="project-tracking__brief-meta">
                @if ($project->timeline)<div><span>Délai souhaité</span><strong>{{ $project->timeline }}</strong></div>@endif
                @if ($project->budget)<div><span>Budget indicatif</span><strong>{{ $project->budget }}</strong></div>@endif
              </div>
            @endif
          </section>

          <section class="project-tracking__card project-tracking__conversation" aria-labelledby="project-conversation-title">
            <div class="project-tracking__card-heading">
              <span class="project-tracking__card-icon project-tracking__card-icon--chat" aria-hidden="true">02</span>
              <div><span class="project-tracking__eyebrow">Gardons le lien</span><h2 id="project-conversation-title">Échanges avec EXPOSIS</h2></div>
              <span class="project-tracking__conversation-count">{{ $project->messages->where('is_internal', false)->count() }} message{{ $project->messages->where('is_internal', false)->count() === 1 ? '' : 's' }}</span>
            </div>

            @if ($project->messages->where('is_internal', false)->isNotEmpty())
              <div class="project-tracking__messages">
                @foreach ($project->messages->where('is_internal', false)->sortBy('created_at') as $message)
                  <article @class([
                      'project-tracking__message',
                      'project-tracking__message--team' => $message->user_id !== $project->user_id,
                  ])>
                    <span class="project-tracking__message-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($message->user?->name ?? 'E', 0, 1)) }}</span>
                    <div class="project-tracking__message-content">
                      <div class="project-tracking__message-meta"><strong>{{ $message->user?->name ?? 'Équipe EXPOSIS' }}</strong><time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->translatedFormat('j M Y · H:i') }}</time></div>
                      <p>{{ $message->body }}</p>
                    </div>
                  </article>
                @endforeach
              </div>
            @else
              <div class="project-tracking__empty-messages">
                <span aria-hidden="true">✳</span>
                <p>Pas encore de message. Vous pouvez écrire à l’équipe si vous souhaitez ajouter un détail utile.</p>
              </div>
            @endif

            <form action="{{ route('projects.messages.store', $project) }}" class="project-tracking__message-form" method="POST">
              @csrf
              <label for="message">Votre message à l’équipe</label>
              <textarea id="message" maxlength="3000" name="body" placeholder="Une question, une précision ou une nouvelle idée…" required>{{ old('body') }}</textarea>
              <div class="project-tracking__form-footer">
                <span>Votre message sera visible par vous et l’équipe EXPOSIS.</span>
                <button type="submit">Envoyer le message <span aria-hidden="true">↗</span></button>
              </div>
            </form>
          </section>

          @if ($project->evaluation?->shared)
            <section class="project-tracking__evaluation" aria-labelledby="project-evaluation-title">
              <div class="project-tracking__evaluation-top">
                <span class="project-tracking__evaluation-icon" aria-hidden="true">✳</span>
                <div><span class="project-tracking__eyebrow">Retour de l’équipe</span><h2 id="project-evaluation-title">Évaluation de faisabilité</h2></div>
                <strong class="project-tracking__score">{{ $project->evaluation->score() }}<span>/100</span></strong>
              </div>
              <div class="project-tracking__score-track" role="img" aria-label="Score de faisabilité : {{ $project->evaluation->score() }} sur 100"><span style="width: {{ $project->evaluation->score() }}%"></span></div>
              <p class="project-tracking__recommendation">{{ \App\Models\Evaluation::RECOMMENDATIONS[$project->evaluation->recommendation] ?? $project->evaluation->recommendation }}</p>
              <p class="project-tracking__evaluation-summary">{{ $project->evaluation->summary }}</p>
            </section>
          @endif
        </div>

        <aside class="project-tracking__sidebar" aria-label="Informations sur votre demande">
          <section class="project-tracking__side-card">
            <span class="project-tracking__eyebrow">Fiche de suivi</span>
            <h2>Les repères utiles.</h2>
            <dl>
              <div><dt>Référence</dt><dd>{{ $project->reference }}</dd></div>
              <div><dt>Type de demande</dt><dd>{{ $project->typeLabel() }}</dd></div>
              <div><dt>Statut</dt><dd><span class="project-tracking__side-status project-tracking__side-status--{{ $statusTone }}">{{ $project->statusLabel() }}</span></dd></div>
              <div><dt>Confidentialité</dt><dd>{{ $project->confidential ? 'Informations signalées confidentielles' : 'Demande standard' }}</dd></div>
              <div><dt>Accord de confidentialité</dt><dd>{{ \App\Models\Project::NDA[$project->nda_status] ?? $project->nda_status }}</dd></div>
            </dl>
          </section>
          <section class="project-tracking__help-card">
            <span aria-hidden="true">∞</span>
            <p>Une précision peut faire avancer les choses.</p>
            <small>Écrivez directement à l’équipe dans votre fil de discussion.</small>
          </section>
          <a class="project-tracking__back-link" href="{{ route('projects.index') }}">← Voir toutes mes demandes</a>
        </aside>
      </div>
    </div>
  </main>
</x-app-layout>
