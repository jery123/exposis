<x-admin-layout :title="$item->exists ? 'Modifier un projet communautaire' : 'Nouveau projet communautaire'">
  <a class="admin-back-link" href="{{ route('admin.public-projects.index') }}">← Retour aux projets communautaires</a>
  <section class="admin-page-heading">
    <div><span class="admin-eyebrow">Catalogue public</span><h2>{{ $item->exists ? 'Modifier le projet' : 'Ajouter un projet' }}</h2><p>Complétez la fiche, vérifiez les fichiers et choisissez si elle doit être publiée.</p></div>
  </section>
  @if ($errors->any())
    <p class="admin-alert admin-alert--error" role="alert">Certains champs sont à corriger. Consultez les messages sous les champs concernés.</p>
  @endif
  <form class="admin-form-card admin-project-form" method="POST" action="{{ $item->exists ? route('admin.public-projects.update', $item) : route('admin.public-projects.store') }}">
    @csrf
    @if ($item->exists) @method('PUT') @endif
    <div class="admin-form-field">
      <label for="title">Titre du projet <span aria-hidden="true">*</span></label>
      <input id="title" name="title" maxlength="150" value="{{ old('title', $item->title) }}" required>
      @error('title')<span class="admin-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="admin-form-field">
      <label for="slug">Adresse personnalisée <span class="admin-muted">(facultatif, générée automatiquement si vide)</span></label>
      <input id="slug" name="slug" value="{{ old('slug', $item->slug) }}">
      @error('slug')<span class="admin-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="admin-form-grid admin-form-grid--two">
      <div class="admin-form-field">
        <label for="pole">Domaine <span aria-hidden="true">*</span></label>
        <select id="pole" name="pole" required>
          @foreach (\App\Models\PublicProject::POLES as $pole)
            <option value="{{ $pole }}" @selected(old('pole', $item->pole) === $pole)>{{ $pole }}</option>
          @endforeach
        </select>
        @error('pole')<span class="admin-field-error">{{ $message }}</span>@enderror
      </div>
      <div class="admin-form-field">
        <label for="status">État du projet <span aria-hidden="true">*</span></label>
        <select id="status" name="status" required>
          @foreach (\App\Models\PublicProject::STATUSES as $status)
            <option value="{{ $status }}" @selected(old('status', $item->status) === $status)>{{ $status }}</option>
          @endforeach
        </select>
        @error('status')<span class="admin-field-error">{{ $message }}</span>@enderror
      </div>
    </div>
    <div class="admin-form-field">
      <label for="summary">Résumé <span aria-hidden="true">*</span> <span class="admin-muted">(400 caractères maximum)</span></label>
      <textarea id="summary" name="summary" rows="3" maxlength="400" required>{{ old('summary', $item->summary) }}</textarea>
      @error('summary')<span class="admin-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="admin-form-field">
      <label for="team_members">Participants <span class="admin-muted">(un nom par ligne)</span></label>
      <textarea id="team_members" name="team_members" rows="4" maxlength="2000">{{ old('team_members', implode("\n", $item->team_members ?? [])) }}</textarea>
      @error('team_members')<span class="admin-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="admin-form-field">
      <label for="body">Présentation détaillée (Markdown) <span aria-hidden="true">*</span></label>
      <textarea id="body" name="body" rows="12" required>{{ old('body', $item->body) }}</textarea>
      @error('body')<span class="admin-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="admin-form-field">
      <label for="repo_url">Lien vers le projet <span class="admin-muted">(facultatif)</span></label>
      <input id="repo_url" name="repo_url" type="url" value="{{ old('repo_url', $item->repo_url) }}">
      @error('repo_url')<span class="admin-field-error">{{ $message }}</span>@enderror
    </div>
    @if ($item->images->isNotEmpty())
      <fieldset class="admin-attachment-panel">
        <legend>Images de la proposition <span class="admin-muted">({{ $item->images->count() }})</span></legend>
        <div class="admin-image-grid">
          @foreach ($item->images as $image)
            <a href="{{ route('admin.public-projects.images.show', [$item, $image]) }}" target="_blank" rel="noreferrer">
              <img src="{{ route('admin.public-projects.images.show', [$item, $image]) }}" alt="Image envoyée avec la proposition de {{ $item->title }}">
              <span>{{ $image->original_name }}</span>
            </a>
          @endforeach
        </div>
      </fieldset>
    @endif
    @if ($item->report)
      <section class="admin-attachment-panel admin-report-review" aria-labelledby="report-review-heading">
        <div>
          <h3 id="report-review-heading">Rapport de la communauté</h3>
          <p>{{ $item->report->original_name }} · {{ $item->report->approved_at ? 'accessible aux membres connectés' : 'privé, en attente de validation' }}</p>
        </div>
        <a class="admin-text-link" href="{{ route('admin.public-projects.reports.show', [$item, $item->report]) }}">Télécharger pour examen ↗</a>
        <label class="admin-check-option"><input type="checkbox" name="approve_report" value="1" @checked(old('approve_report', (bool) $item->report->approved_at))> Autoriser l’accès au rapport pour les membres connectés</label>
        <p>Le rapport est accessible uniquement si cette option est cochée et que le projet est publié. Tout remplacement du fichier retire cette autorisation jusqu’à une nouvelle validation.</p>
      </section>
    @endif
    <div class="admin-form-options">
      <label class="admin-check-option"><input type="checkbox" name="is_sample" value="1" @checked(old('is_sample', $item->is_sample))> Fiche d’exemple</label>
      <label class="admin-check-option"><input type="checkbox" name="publish" value="1" @checked(old('publish', (bool) $item->published_at))> Publier sur le site</label>
    </div>
    <div class="admin-form-actions">
      <a class="admin-button admin-button--secondary" href="{{ route('admin.public-projects.index') }}">Annuler</a>
      <button class="admin-button admin-button--primary" type="submit">Enregistrer le projet <span aria-hidden="true">↗</span></button>
    </div>
  </form>
</x-admin-layout>
