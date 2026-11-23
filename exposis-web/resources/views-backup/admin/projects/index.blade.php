<x-admin-layout title="Demandes reçues">
  <section class="admin-page-heading">
    <div><span class="admin-eyebrow">Gestion des projets</span><h2>Demandes reçues</h2><p>Recherchez, évaluez et suivez les projets proposés à EXPOSIS.</p></div>
    <a class="admin-button admin-button--secondary" href="{{ route('admin.projects.export') }}">Exporter en CSV <span aria-hidden="true">↗</span></a>
  </section>
  <form class="admin-search-form" method="GET">
    <label class="sr-only" for="project-search">Titre ou référence</label><input id="project-search" name="q" value="{{ request('q') }}" placeholder="Titre ou référence">
    <label class="sr-only" for="project-status">Filtrer par statut</label><select id="project-status" name="status"><option value="">Tous les statuts</option>@foreach (\App\Models\Project::STATUSES as $key => $label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select>
    <button class="admin-button admin-button--dark" type="submit">Filtrer <span aria-hidden="true">⌕</span></button>
  </form>
  <div class="admin-table-wrap">
    <table class="admin-table"><thead><tr><th>Référence</th><th>Projet</th><th>Organisation</th><th>Statut</th><th>Score</th></tr></thead><tbody>
      @forelse ($projects as $project)
        <tr><td><a class="admin-text-link" href="{{ route('admin.projects.show', $project) }}">{{ $project->reference }}</a></td><td><strong>{{ $project->title }}</strong> @if($project->confidential)<span class="admin-confidential" title="Confidentiel">Confidentiel</span>@endif</td><td>{{ $project->user?->organization_name ?? $project->user?->name ?? $project->submitter_organization ?? $project->submitter_name }}</td><td><span class="admin-pill">{{ $project->statusLabel() }}</span></td><td>{{ $project->evaluation?->score() ?? '—' }}</td></tr>
      @empty
        <tr><td colspan="5" class="admin-empty">Aucune demande ne correspond à ces filtres.</td></tr>
      @endforelse
    </tbody></table>
  </div>
  <div class="admin-pagination">{{ $projects->links() }}</div>
</x-admin-layout>
