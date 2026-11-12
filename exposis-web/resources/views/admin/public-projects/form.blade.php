<x-admin-layout :title="$item->exists ? 'Modifier un projet communautaire' : 'Nouveau projet communautaire'">
  @php $f = 'w-full rounded-xl border-gray-400'; @endphp
  <div class="mx-auto max-w-3xl p-6">
    <form method="POST" action="{{ $item->exists ? route('admin.public-projects.update', $item) : route('admin.public-projects.store') }}" class="grid gap-4 rounded-2xl border bg-white p-6">
      @csrf @if($item->exists) @method('PUT') @endif
      <div><x-input-label for="title" value="Titre *"/><input id="title" name="title" maxlength="150" class="{{ $f }}" value="{{ old('title', $item->title) }}" required><x-input-error :messages="$errors->get('title')"/></div>
      <div><x-input-label for="slug" value="Adresse (slug, automatique si vide)"/><input id="slug" name="slug" class="{{ $f }}" value="{{ old('slug', $item->slug) }}"><x-input-error :messages="$errors->get('slug')"/></div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div><x-input-label for="pole" value="Domaine *"/><select id="pole" name="pole" class="{{ $f }}">@foreach (\App\Models\PublicProject::POLES as $p)<option @selected(old('pole', $item->pole) === $p)>{{ $p }}</option>@endforeach</select></div>
        <div><x-input-label for="status" value="Statut *"/><select id="status" name="status" class="{{ $f }}">@foreach (\App\Models\PublicProject::STATUSES as $s)<option @selected(old('status', $item->status) === $s)>{{ $s }}</option>@endforeach</select></div></div>
      <div><x-input-label for="summary" value="Résumé * (400 max)"/><textarea id="summary" name="summary" rows="3" maxlength="400" class="{{ $f }}" required>{{ old('summary', $item->summary) }}</textarea><x-input-error :messages="$errors->get('summary')"/></div>
      <div><x-input-label for="team_members" value="Participants (un nom par ligne)"/><textarea id="team_members" name="team_members" rows="4" maxlength="2000" class="{{ $f }}">{{ old('team_members', implode("\n", $item->team_members ?? [])) }}</textarea><x-input-error :messages="$errors->get('team_members')"/></div>
      <div><x-input-label for="body" value="Contenu (Markdown) *"/><textarea id="body" name="body" rows="12" class="{{ $f }} font-mono text-sm" required>{{ old('body', $item->body) }}</textarea><x-input-error :messages="$errors->get('body')"/></div>
      <div><x-input-label for="repo_url" value="Lien du projet (optionnel)"/><input id="repo_url" name="repo_url" type="url" class="{{ $f }}" value="{{ old('repo_url', $item->repo_url) }}"><x-input-error :messages="$errors->get('repo_url')"/></div>
      @if ($item->images->isNotEmpty())
        <fieldset class="grid gap-3">
          <legend class="font-semibold">Images de la proposition ({{ $item->images->count() }})</legend>
          <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach ($item->images as $image)
              <a href="{{ route('admin.public-projects.images.show', [$item, $image]) }}" target="_blank" rel="noreferrer">
                <img class="aspect-video w-full rounded-lg border object-cover" src="{{ route('admin.public-projects.images.show', [$item, $image]) }}" alt="Image envoyée avec la proposition de {{ $item->title }}">
                <span class="mt-1 block truncate text-sm">{{ $image->original_name }}</span>
              </a>
            @endforeach
          </div>
        </fieldset>
      @endif
      @if ($item->report)
        <section class="grid gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4" aria-labelledby="report-review-heading">
          <div>
            <h2 id="report-review-heading" class="font-bold">Rapport de la communauté</h2>
            <p class="text-sm text-gray-600">{{ $item->report->original_name }} · {{ $item->report->approved_at ? 'accessible au public' : 'privé, en attente de validation' }}</p>
          </div>
          <a class="w-fit underline" href="{{ route('admin.public-projects.reports.show', [$item, $item->report]) }}">Télécharger pour examen</a>
          <label class="flex gap-2"><input type="checkbox" name="approve_report" value="1" @checked(old('approve_report', (bool) $item->report->approved_at))> Autoriser le téléchargement public du rapport</label>
          <p class="text-sm text-gray-600">Le rapport ne sera public que si cette option est cochée et que le projet est publié. Tout remplacement du fichier retire cette autorisation jusqu’à une nouvelle validation.</p>
        </section>
      @endif
      <label class="flex gap-2"><input type="checkbox" name="is_sample" value="1" @checked(old('is_sample', $item->is_sample))> Fiche d’exemple</label>
      <label class="flex gap-2"><input type="checkbox" name="publish" value="1" @checked(old('publish', (bool) $item->published_at))> Publier sur le site</label>
      <button class="rounded-xl bg-orange-600 px-5 py-3 font-semibold text-white">Enregistrer</button>
    </form>
  </div>
</x-admin-layout>
