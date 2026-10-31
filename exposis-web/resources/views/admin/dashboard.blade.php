<x-app-layout>
  <x-slot name="header"><h1 class="text-xl font-extrabold">Tableau de bord</h1></x-slot>
  <div class="mx-auto max-w-6xl space-y-6 p-6">
    <nav class="flex flex-wrap gap-3 text-sm font-semibold" aria-label="Administration">
      <a class="underline" href="{{ route('admin.projects.index') }}">Projets reçus</a>
      <a class="underline" href="{{ route('admin.projects.export') }}">Exporter en CSV</a>
      <a class="underline" href="{{ route('admin.public-projects.index') }}">Projets publics</a></nav>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-600">Total</p><p class="text-3xl font-extrabold">{{ $total }}</p></div>
      <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-600">À examiner</p><p class="text-3xl font-extrabold">{{ $counts['submitted'] ?? 0 }}</p></div>
      <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-600">Informations demandées</p><p class="text-3xl font-extrabold">{{ $counts['needs_info'] ?? 0 }}</p></div>
      <a class="rounded-2xl border bg-white p-5 hover:border-orange-600" href="{{ route('admin.public-projects.index') }}"><p class="text-sm text-gray-600">Projets communautaires à valider</p><p class="text-3xl font-extrabold">{{ $pendingPublicProjects }}</p></a>
      <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-gray-600">Score moyen</p><p class="text-3xl font-extrabold">{{ $avgScore ?? '—' }}</p></div></div>
    <section class="rounded-2xl border bg-white p-6"><h2 class="font-extrabold">En attente d’une action</h2>
      <ul class="mt-3 divide-y">@forelse ($waiting as $p)<li class="py-2"><a class="underline" href="{{ route('admin.projects.show', $p) }}">{{ $p->reference }}</a> · {{ $p->title }} · {{ $p->user?->organization_name ?? $p->submitter_organization ?? $p->submitter_name }} · {{ $p->statusLabel() }}</li>
        @empty <li class="py-2 text-gray-600">Rien en attente.</li>@endforelse</ul></section>
  </div>
</x-app-layout>
