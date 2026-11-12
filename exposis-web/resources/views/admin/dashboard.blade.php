<x-admin-layout title="Vue d’ensemble">
  <section class="admin-page-heading">
    <div><span class="admin-eyebrow">Votre espace de pilotage</span><h2>Bonjour, {{ Auth::user()->name }}</h2><p>Un aperçu clair des demandes et des projets de la communauté EXPOSIS.</p></div>
  </section>
  <div class="admin-stat-grid">
    <article class="admin-stat-card"><span class="admin-stat-card__label">Toutes les demandes</span><strong>{{ $total }}</strong><span>Depuis la création de l’espace</span></article>
    <article class="admin-stat-card"><span class="admin-stat-card__label">À examiner</span><strong>{{ $counts['submitted'] ?? 0 }}</strong><span>Demandes nouvellement reçues</span></article>
    <article class="admin-stat-card"><span class="admin-stat-card__label">Informations attendues</span><strong>{{ $counts['needs_info'] ?? 0 }}</strong><span>En attente d’un retour du porteur</span></article>
    <a class="admin-stat-card admin-stat-card--linked" href="{{ route('admin.public-projects.index') }}"><span class="admin-stat-card__label">Projets communautaires à valider</span><strong>{{ $pendingPublicProjects }}</strong><span>Ouvrir la file de validation ↗</span></a>
    <article class="admin-stat-card"><span class="admin-stat-card__label">Score moyen</span><strong>{{ $avgScore ?? '—' }}</strong><span>Sur les projets évalués</span></article>
  </div>
  <section class="admin-card admin-dashboard-queue">
    <div class="admin-card-heading"><div><span class="admin-eyebrow">À traiter en priorité</span><h2>En attente d’une action</h2></div><a class="admin-text-link" href="{{ route('admin.projects.index') }}">Toutes les demandes ↗</a></div>
    <div class="admin-queue-list">
      @forelse ($waiting as $project)
        <a class="admin-queue-item" href="{{ route('admin.projects.show', $project) }}"><span class="admin-queue-item__reference">{{ $project->reference }}</span><span class="admin-queue-item__title"><strong>{{ $project->title }}</strong><small>{{ $project->user?->organization_name ?? $project->submitter_organization ?? $project->submitter_name }}</small></span><span class="admin-pill">{{ $project->statusLabel() }}</span><span class="admin-queue-item__arrow" aria-hidden="true">↗</span></a>
      @empty
        <p class="admin-empty">Aucune demande ne nécessite d’action pour le moment.</p>
      @endforelse
    </div>
  </section>
</x-admin-layout>
