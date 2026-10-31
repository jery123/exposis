<x-app-layout>
  <x-slot name="header"><h1 class="text-xl font-extrabold">Projets reçus</h1></x-slot>
  <div class="mx-auto max-w-6xl p-6">
    <form class="mb-4 flex flex-wrap gap-3"><input name="q" value="{{ request('q') }}" placeholder="Titre ou référence" class="rounded-xl border-gray-400">
      <select name="status" class="rounded-xl border-gray-400"><option value="">Tous les statuts</option>@foreach (\App\Models\Project::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach</select>
      <button class="rounded-xl bg-gray-900 px-4 text-white">Filtrer</button></form>
    <table class="w-full overflow-hidden rounded-2xl border bg-white text-left text-sm"><thead class="bg-gray-100"><tr><th class="p-3">Réf.</th><th>Projet</th><th>Organisation</th><th>Statut</th><th>Score</th></tr></thead><tbody>
      @foreach ($projects as $p)<tr class="border-t hover:bg-gray-50"><td class="p-3"><a class="font-semibold underline" href="{{ route('admin.projects.show', $p) }}">{{ $p->reference }}</a></td>
        <td>{{ $p->title }} @if($p->confidential)<span title="Confidentiel">🔒</span>@endif</td><td>{{ $p->user?->organization_name ?? $p->user?->name ?? $p->submitter_organization ?? $p->submitter_name }}</td><td>{{ $p->statusLabel() }}</td><td>{{ $p->evaluation?->score() ?? '—' }}</td></tr>@endforeach
    </tbody></table><div class="mt-4">{{ $projects->links() }}</div>
  </div>
</x-app-layout>
