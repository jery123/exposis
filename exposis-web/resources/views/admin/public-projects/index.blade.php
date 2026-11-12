<x-admin-layout title="Projets communautaires">
  @if (session('status'))<p class="admin-alert admin-alert--success" role="status">{{ session('status') }}</p>@endif
  <section class="admin-page-heading">
    <div><span class="admin-eyebrow">Catalogue public</span><h2>Projets communautaires</h2><p>Examinez les propositions et gérez leur publication ainsi que la validation des rapports.</p></div>
    <a class="admin-button admin-button--primary" href="{{ route('admin.public-projects.create') }}">Nouveau projet <span aria-hidden="true">＋</span></a>
  </section>
  <div class="admin-community-list">
    @forelse ($items as $item)
      <article class="admin-card admin-community-item">
        <div><div class="admin-community-item__top"><h3>{{ $item->title }}</h3><span class="admin-pill {{ $item->published_at ? 'admin-pill--success' : 'admin-pill--accent' }}">{{ $item->published_at ? 'Publié' : 'À examiner' }}</span></div>
          <p>{{ $item->pole }} · {{ $item->status }} · {{ $item->images_count }} image(s) @if($item->report) · rapport {{ $item->report->approved_at ? 'validé' : 'à valider' }}@endif @if($item->user) · proposé par {{ $item->user->name }}@endif</p>
        </div>
        <div class="admin-community-item__actions"><a class="admin-text-link" href="{{ route('admin.public-projects.edit', $item) }}">Examiner / modifier ↗</a>
          <form method="POST" action="{{ route('admin.public-projects.destroy', $item) }}" onsubmit="return confirm('Supprimer ce projet ?')">@csrf @method('DELETE')<button class="admin-danger-link" type="submit">Supprimer</button></form>
        </div>
      </article>
    @empty
      <p class="admin-empty admin-card">Aucun projet communautaire enregistré.</p>
    @endforelse
  </div>
  <div class="admin-pagination">{{ $items->links() }}</div>
</x-admin-layout>
