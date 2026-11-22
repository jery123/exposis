<x-admin-layout :title="'Demande '.$project->reference">
  @php($evaluation = $project->evaluation)
  <a class="admin-back-link" href="{{ route('admin.projects.index') }}">← Retour aux demandes</a>
  @if (session('status'))
    <p class="admin-alert admin-alert--success" role="status">{{ session('status') }}</p>
  @endif
  <div class="admin-project-detail">
    <section class="admin-card admin-project-summary">
      <div class="admin-project-summary__heading">
        <div><span class="admin-eyebrow">Demande {{ $project->reference }}</span><h2>{{ $project->title }}</h2></div>
        @if ($project->confidential)<span class="admin-pill admin-pill--accent">Confidentiel</span>@endif
      </div>
      <dl class="admin-project-contact">
        <div><dt>Organisation</dt><dd>{{ $project->user?->organization_name ?? $project->submitter_organization ?? 'Porteur de projet' }}</dd></div>
        <div><dt>Contact</dt><dd>{{ $project->user?->name ?? $project->submitter_name }}</dd></div>
        <div><dt>E-mail</dt><dd><a href="mailto:{{ $project->user?->email ?? $project->submitter_email }}">{{ $project->user?->email ?? $project->submitter_email }}</a></dd></div>
        <div><dt>Téléphone</dt><dd>{{ $project->user?->phone ?? $project->submitter_phone ?? '—' }}</dd></div>
        <div><dt>Type de demande</dt><dd>{{ $project->typeLabel() }}</dd></div>
        <div><dt>Délai</dt><dd>{{ $project->timeline ?? '—' }}</dd></div>
        <div><dt>Budget</dt><dd>{{ $project->budget ?? '—' }}</dd></div>
      </dl>
      <div class="admin-project-text">
        <h3>Problème à résoudre</h3>
        <p>{{ $project->problem }}</p>
      </div>
      <div class="admin-project-text">
        <h3>Description du projet</h3>
        <p>{{ $project->description }}</p>
      </div>
    </section>
    <div class="admin-project-actions">
      <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="admin-form-card admin-project-status-form">
        @csrf @method('PATCH')
        <h2>Suivi de la demande</h2>
        <div class="admin-form-grid admin-form-grid--two">
          <div class="admin-form-field">
            <label for="status">Statut</label>
            <select id="status" name="status">
              @foreach (\App\Models\Project::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected($project->status === $key)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="admin-form-field">
            <label for="nda">NDA</label>
            <select id="nda" name="nda_status">
              @foreach (\App\Models\Project::NDA as $key => $label)
                <option value="{{ $key }}" @selected($project->nda_status === $key)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <button class="admin-button admin-button--dark" type="submit">Mettre à jour</button>
      </form>
      <form method="POST" action="{{ route('admin.projects.evaluate', $project) }}" class="admin-form-card admin-evaluation-form">
        @csrf @method('PUT')
        <div class="admin-card-heading">
          <div><span class="admin-eyebrow">Analyse interne</span><h2>Évaluation de faisabilité</h2></div>
          @if ($evaluation)<span class="admin-pill admin-pill--accent">{{ $evaluation->score() }}/100</span>@endif
        </div>
        <div class="admin-evaluation-grid">
          @foreach (['technical' => 'Faisabilité technique', 'resources' => 'Ressources disponibles', 'impact' => 'Impact utile', 'risk' => 'Niveau de risque'] as $key => $label)
            <div class="admin-form-field">
              <label for="{{ $key }}">{{ $label }} <span class="admin-muted">(note de 1 à 5)</span></label>
              <select id="{{ $key }}" name="{{ $key }}">
                @for ($score = 1; $score <= 5; $score++)
                  <option value="{{ $score }}" @selected(old($key, $evaluation?->$key) == $score)>{{ $score }}</option>
                @endfor
              </select>
            </div>
          @endforeach
        </div>
        <div class="admin-form-field">
          <label for="recommendation">Recommandation</label>
          <select id="recommendation" name="recommendation">
            @foreach (\App\Models\Evaluation::RECOMMENDATIONS as $key => $label)
              <option value="{{ $key }}" @selected(old('recommendation', $evaluation?->recommendation) === $key)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="admin-form-field">
          <label for="summary">Synthèse <span class="admin-muted">(visible par l’organisation si partagée)</span></label>
          <textarea id="summary" name="summary" rows="6" required>{{ old('summary', $evaluation?->summary) }}</textarea>
          @error('summary')<span class="admin-field-error">{{ $message }}</span>@enderror
        </div>
        <label class="admin-check-option"><input type="checkbox" name="shared" value="1" @checked(old('shared', $evaluation?->shared))> Partager cette évaluation avec l’organisation</label>
        <button class="admin-button admin-button--primary" type="submit">Enregistrer l’évaluation <span aria-hidden="true">↗</span></button>
      </form>
    </div>
  </div>
</x-admin-layout>
