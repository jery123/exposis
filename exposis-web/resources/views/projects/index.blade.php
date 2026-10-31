<x-app-layout>
  <x-slot name="header"><h1 class="text-xl font-extrabold">Mes projets</h1></x-slot>
  <div class="mx-auto max-w-5xl p-6">
    @if (session('status'))<p class="mb-4 rounded-lg bg-orange-100 p-3 text-sm font-medium text-orange-900" role="status">{{ session('status') }}</p>@endif
    <a href="{{ route('projects.create') }}" class="inline-block rounded-xl bg-orange-600 px-5 py-2.5 font-semibold text-white hover:bg-orange-700">Soumettre un projet</a>
    <a href="{{ route('community.projects.create') }}" class="ms-2 inline-block rounded-xl border border-gray-900 px-5 py-2.5 font-semibold">Proposer un projet communautaire</a>
    <ul class="mt-6 divide-y rounded-2xl border bg-white">
      @forelse ($projects as $p)
        <li><a href="{{ route('projects.show', $p) }}" class="flex flex-wrap items-center justify-between gap-2 p-5 hover:bg-gray-50">
          <span><b>{{ $p->title }}</b><br><small class="text-gray-600">{{ $p->reference }} · {{ $p->typeLabel() }}</small></span>
          <span class="rounded-full border px-3 py-1 text-sm font-semibold">{{ $p->statusLabel() }}</span></a></li>
      @empty <li class="p-6 text-gray-600">Aucun projet pour l’instant.</li>
      @endforelse
    </ul>
    <div class="mt-4">{{ $projects->links() }}</div>
  </div>
</x-app-layout>
